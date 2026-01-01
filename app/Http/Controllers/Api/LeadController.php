<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Client;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    /**
     * Get all leads with filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $query = Lead::ofCompany($companyId)
            ->with(['assignedTo:id,name', 'status:id,name,color']);

        // Role-based filtering
        if (!$user->isAdmin() && !$user->isManager()) {
            $query->where('assigned_to', $user->id);
        }

        // Filters - use filled() to ignore empty strings
        if ($request->filled('status_id')) {
            $query->where('lead_status_id', $request->status_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $leads = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    /**
     * Get leads grouped by status for Kanban view.
     */
    public function kanban(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $statuses = LeadStatus::ofCompany($companyId)
            ->orderBy('order')
            ->get();

        $kanbanData = $statuses->map(function ($status) use ($user, $companyId) {
            $query = Lead::ofCompany($companyId)
                ->where('lead_status_id', $status->id)
                ->with(['assignedTo:id,name']);

            if (!$user->isAdmin() && !$user->isManager()) {
                $query->where('assigned_to', $user->id);
            }

            $leads = $query->orderBy('priority')
                ->orderBy('updated_at', 'desc')
                ->limit(50)
                ->get();

            return [
                'id' => $status->id,
                'name' => $status->name,
                'color' => $status->color,
                'is_won' => $status->is_won,
                'is_lost' => $status->is_lost,
                'leads' => $leads->map(function ($lead) {
                    return [
                        'id' => $lead->id,
                        'name' => $lead->name,
                        'phone' => $lead->phone,
                        'budget_range' => $lead->budget_range,
                        'location_preference' => $lead->location_preference,
                        'property_type' => $lead->property_type,
                        'priority' => $lead->priority,
                        'assigned_to' => $lead->assignedTo?->name,
                        'created_at' => $lead->created_at->diffForHumans(),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $kanbanData,
        ]);
    }

    /**
     * Create a new lead.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'alternate_phone' => 'nullable|string|max:20',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'location_preference' => 'nullable|string|max:255',
            'preferred_locations' => 'nullable|array',
            'property_type' => 'nullable|string|max:100',
            'purpose' => 'nullable|string|in:buy,rent,investment',
            'bhk' => 'nullable|string|max:20',
            'area_min' => 'nullable|numeric|min:0',
            'area_max' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:100',
            'source_details' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|in:1,2,3',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'lead_status_id' => 'nullable|exists:lead_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get default status if not provided
        $statusId = $request->lead_status_id;
        if (!$statusId) {
            $defaultStatus = LeadStatus::ofCompany($user->company_id)
                ->where('is_default', true)
                ->first();
            $statusId = $defaultStatus?->id;
        }

        $lead = Lead::create([
            'company_id' => $user->company_id,
            'lead_status_id' => $statusId,
            'assigned_to' => $request->assigned_to ?? $user->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'alternate_phone' => $request->alternate_phone,
            'budget_min' => $request->budget_min,
            'budget_max' => $request->budget_max,
            'location_preference' => $request->location_preference,
            'preferred_locations' => $request->preferred_locations,
            'property_type' => $request->property_type,
            'purpose' => $request->purpose ?? 'buy',
            'bhk' => $request->bhk,
            'area_min' => $request->area_min,
            'area_max' => $request->area_max,
            'source' => $request->source ?? 'manual',
            'source_details' => $request->source_details,
            'priority' => $request->priority ?? 2,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('created', "Added new lead: {$lead->name}", $lead);

        $lead->load(['assignedTo:id,name', 'status:id,name,color']);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    /**
     * Get a single lead.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $lead = Lead::ofCompany($user->company_id)
            ->with([
                'assignedTo:id,name,phone,email',
                'status:id,name,color',
                'followUps' => function ($q) {
                    $q->orderBy('scheduled_at', 'desc')->limit(10);
                },
                'followUps.user:id,name',
                'propertyVisits' => function ($q) {
                    $q->with('property:id,title')->orderBy('visit_date', 'desc')->limit(5);
                },
            ])
            ->find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        // Check access
        if (!$user->canAccessResource($lead)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lead',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'lead' => $lead,
                'whatsapp_link' => $lead->whatsapp_link,
                'phone_link' => $lead->phone_link,
                'budget_range' => $lead->budget_range,
                'is_converted' => $lead->isConverted(),
            ],
        ]);
    }

    /**
     * Update a lead.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $lead = Lead::ofCompany($user->company_id)->find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        if (!$user->canAccessResource($lead)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lead',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'nullable|email|max:255',
            'alternate_phone' => 'nullable|string|max:20',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'location_preference' => 'nullable|string|max:255',
            'preferred_locations' => 'nullable|array',
            'property_type' => 'nullable|string|max:100',
            'purpose' => 'nullable|string|in:buy,rent,investment',
            'bhk' => 'nullable|string|max:20',
            'area_min' => 'nullable|numeric|min:0',
            'area_max' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:100',
            'source_details' => 'nullable|string|max:255',
            'priority' => 'nullable|integer|in:1,2,3',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'lead_status_id' => 'nullable|exists:lead_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lead->update($request->all());

        ActivityLog::log('updated', "Updated lead: {$lead->name}", $lead);

        $lead->load(['assignedTo:id,name', 'status:id,name,color']);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'data' => $lead,
        ]);
    }

    /**
     * Update lead status.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        $lead = Lead::ofCompany($user->company_id)->find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        if (!$user->canAccessResource($lead)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lead',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'lead_status_id' => 'required|exists:lead_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $lead->status?->name;
        $lead->update(['lead_status_id' => $request->lead_status_id]);
        $lead->load('status:id,name,color');

        ActivityLog::log('status_changed', "Changed lead status from {$oldStatus} to {$lead->status->name}", $lead);

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated',
            'data' => $lead,
        ]);
    }

    /**
     * Convert lead to client.
     */
    public function convertToClient(Request $request, $id)
    {
        $user = $request->user();

        $lead = Lead::ofCompany($user->company_id)->find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        if (!$user->canAccessResource($lead)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lead',
            ], 403);
        }

        if ($lead->isConverted()) {
            return response()->json([
                'success' => false,
                'message' => 'Lead is already converted to a client',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:buyer,seller,both',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $client = Client::createFromLead($lead, $request->only(['type', 'address', 'city', 'notes']));

        ActivityLog::log('converted', "Converted lead {$lead->name} to client", $lead);

        return response()->json([
            'success' => true,
            'message' => 'Lead converted to client successfully',
            'data' => [
                'lead' => $lead->fresh(['status:id,name,color']),
                'client' => $client,
            ],
        ]);
    }

    /**
     * Delete a lead.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $lead = Lead::ofCompany($user->company_id)->find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        if (!$user->isAdmin() && !$user->isManager()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete leads',
            ], 403);
        }

        $leadName = $lead->name;
        $lead->delete();

        ActivityLog::log('deleted', "Deleted lead: {$leadName}");

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully',
        ]);
    }

    /**
     * Get lead sources for dropdown.
     */
    public function sources(Request $request)
    {
        $sources = [
            'manual' => 'Manual Entry',
            'website' => 'Website',
            'referral' => 'Referral',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'google' => 'Google Ads',
            'justdial' => 'JustDial',
            '99acres' => '99acres',
            'magicbricks' => 'MagicBricks',
            'housing' => 'Housing.com',
            'walk_in' => 'Walk-in',
            'other' => 'Other',
        ];

        return response()->json([
            'success' => true,
            'data' => $sources,
        ]);
    }
}
