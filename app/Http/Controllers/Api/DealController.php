<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealController extends Controller
{
    /**
     * Get all deals with filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $query = Deal::ofCompany($companyId)
            ->with([
                'property:id,title,locality,city',
                'buyer:id,name,phone',
                'seller:id,name,phone',
                'handledBy:id,name',
            ]);

        // Role-based filtering
        if (!$user->isAdmin() && !$user->isManager()) {
            $query->where('handled_by', $user->id);
        }

        // Filters
        if ($request->has('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('handled_by')) {
            $query->where('handled_by', $request->handled_by);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('open_only') && $request->boolean('open_only')) {
            $query->open();
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $deals = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $deals->items(),
            'meta' => [
                'current_page' => $deals->currentPage(),
                'last_page' => $deals->lastPage(),
                'per_page' => $deals->perPage(),
                'total' => $deals->total(),
            ],
        ]);
    }

    /**
     * Get deals pipeline view.
     */
    public function pipeline(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $stages = Deal::STAGES;
        $pipeline = [];

        foreach ($stages as $stageKey => $stageName) {
            $query = Deal::ofCompany($companyId)
                ->where('stage', $stageKey)
                ->with(['property:id,title', 'buyer:id,name', 'handledBy:id,name']);

            if (!$user->isAdmin() && !$user->isManager()) {
                $query->where('handled_by', $user->id);
            }

            $deals = $query->orderBy('expected_close_date')
                ->limit(20)
                ->get();

            $pipeline[] = [
                'stage' => $stageKey,
                'name' => $stageName,
                'color' => $this->getStageColor($stageKey),
                'count' => $deals->count(),
                'total_value' => $deals->sum('deal_value'),
                'deals' => $deals->map(function ($deal) {
                    return [
                        'id' => $deal->id,
                        'title' => $deal->title,
                        'value' => $deal->formatted_value,
                        'property' => $deal->property?->title,
                        'buyer' => $deal->buyer?->name,
                        'handled_by' => $deal->handledBy?->name,
                        'expected_close' => $deal->expected_close_date?->format('d M'),
                    ];
                }),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $pipeline,
        ]);
    }

    /**
     * Create a new deal.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'property_id' => 'nullable|exists:properties,id',
            'buyer_id' => 'nullable|exists:clients,id',
            'seller_id' => 'nullable|exists:clients,id',
            'type' => 'required|in:sale,rent',
            'deal_value' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_from' => 'nullable|in:buyer,seller,both',
            'stage' => 'nullable|in:open,negotiation,agreement,documentation,closed_won,closed_lost',
            'expected_close_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'handled_by' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deal = Deal::create([
            'company_id' => $user->company_id,
            'property_id' => $request->property_id,
            'buyer_id' => $request->buyer_id,
            'seller_id' => $request->seller_id,
            'handled_by' => $request->handled_by ?? $user->id,
            'title' => $request->title,
            'type' => $request->type,
            'deal_value' => $request->deal_value,
            'commission_percentage' => $request->commission_percentage,
            'commission_amount' => $request->commission_amount,
            'commission_from' => $request->commission_from ?? 'seller',
            'stage' => $request->stage ?? 'open',
            'expected_close_date' => $request->expected_close_date,
            'notes' => $request->notes,
        ]);

        // Calculate commission if percentage is provided but amount is not
        if ($request->commission_percentage && !$request->commission_amount) {
            $deal->calculateCommission();
            $deal->save();
        }

        ActivityLog::log('created', "Created new deal: {$deal->title}", $deal);

        $deal->load(['property:id,title', 'buyer:id,name', 'seller:id,name', 'handledBy:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Deal created successfully',
            'data' => $deal,
        ], 201);
    }

    /**
     * Get a single deal.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $deal = Deal::ofCompany($user->company_id)
            ->with([
                'property',
                'buyer',
                'seller',
                'handledBy:id,name,phone,email',
            ])
            ->find($id);

        if (!$deal) {
            return response()->json([
                'success' => false,
                'message' => 'Deal not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'deal' => $deal,
                'formatted_value' => $deal->formatted_value,
                'formatted_commission' => $deal->formatted_commission,
                'stage_name' => $deal->stage_name,
                'stage_color' => $deal->stage_color,
            ],
        ]);
    }

    /**
     * Update a deal.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $deal = Deal::ofCompany($user->company_id)->find($id);

        if (!$deal) {
            return response()->json([
                'success' => false,
                'message' => 'Deal not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'property_id' => 'nullable|exists:properties,id',
            'buyer_id' => 'nullable|exists:clients,id',
            'seller_id' => 'nullable|exists:clients,id',
            'type' => 'sometimes|in:sale,rent',
            'deal_value' => 'sometimes|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_from' => 'nullable|in:buyer,seller,both',
            'stage' => 'nullable|in:open,negotiation,agreement,documentation,closed_won,closed_lost',
            'expected_close_date' => 'nullable|date',
            'agreement_date' => 'nullable|date',
            'registration_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'handled_by' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deal->update($request->all());

        // Recalculate commission if needed
        if ($request->has('commission_percentage') || $request->has('deal_value')) {
            $deal->calculateCommission();
            $deal->save();
        }

        ActivityLog::log('updated', "Updated deal: {$deal->title}", $deal);

        $deal->load(['property:id,title', 'buyer:id,name', 'seller:id,name', 'handledBy:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Deal updated successfully',
            'data' => $deal,
        ]);
    }

    /**
     * Update deal stage.
     */
    public function updateStage(Request $request, $id)
    {
        $user = $request->user();

        $deal = Deal::ofCompany($user->company_id)->find($id);

        if (!$deal) {
            return response()->json([
                'success' => false,
                'message' => 'Deal not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'stage' => 'required|in:open,negotiation,agreement,documentation,closed_won,closed_lost',
            'close_reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStage = $deal->stage_name;

        if ($request->stage === 'closed_won') {
            $deal->closeAsWon($request->only(['closed_date', 'registration_date']));
        } elseif ($request->stage === 'closed_lost') {
            $deal->closeAsLost($request->close_reason);
        } else {
            $deal->update(['stage' => $request->stage]);
        }

        ActivityLog::log('stage_changed', "Changed deal stage from {$oldStage} to {$deal->stage_name}", $deal);

        return response()->json([
            'success' => true,
            'message' => 'Deal stage updated',
            'data' => $deal,
        ]);
    }

    /**
     * Update payment status.
     */
    public function updatePayment(Request $request, $id)
    {
        $user = $request->user();

        $deal = Deal::ofCompany($user->company_id)->find($id);

        if (!$deal) {
            return response()->json([
                'success' => false,
                'message' => 'Deal not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,partial,received',
            'amount_received' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deal->update([
            'payment_status' => $request->payment_status,
            'amount_received' => $request->amount_received ?? $deal->amount_received,
        ]);

        ActivityLog::log('payment_updated', "Updated payment status for deal: {$deal->title}", $deal);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated',
            'data' => $deal,
        ]);
    }

    /**
     * Delete a deal.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $deal = Deal::ofCompany($user->company_id)->find($id);

        if (!$deal) {
            return response()->json([
                'success' => false,
                'message' => 'Deal not found',
            ], 404);
        }

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can delete deals',
            ], 403);
        }

        $dealTitle = $deal->title;
        $deal->delete();

        ActivityLog::log('deleted', "Deleted deal: {$dealTitle}");

        return response()->json([
            'success' => true,
            'message' => 'Deal deleted successfully',
        ]);
    }

    /**
     * Get deal statistics.
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $baseQuery = Deal::ofCompany($companyId);
        if (!$user->isAdmin() && !$user->isManager()) {
            $baseQuery->where('handled_by', $user->id);
        }

        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $stats = [
            'total_deals' => (clone $baseQuery)->count(),
            'open_deals' => (clone $baseQuery)->open()->count(),
            'won_this_month' => (clone $baseQuery)->where('stage', 'closed_won')
                ->where('closed_date', '>=', $thisMonth)->count(),
            'revenue_this_month' => (clone $baseQuery)->where('stage', 'closed_won')
                ->where('closed_date', '>=', $thisMonth)->sum('commission_amount'),
            'pipeline_value' => (clone $baseQuery)->open()->sum('deal_value'),
            'expected_commission' => (clone $baseQuery)->open()->sum('commission_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get stage color.
     */
    private function getStageColor($stage)
    {
        return match($stage) {
            'open' => '#3B82F6',
            'negotiation' => '#8B5CF6',
            'agreement' => '#F59E0B',
            'documentation' => '#EC4899',
            'closed_won' => '#10B981',
            'closed_lost' => '#EF4444',
            default => '#6B7280',
        };
    }
}
