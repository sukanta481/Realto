<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Client;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FollowUpController extends Controller
{
    /**
     * Get all follow-ups with filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $query = FollowUp::ofCompany($companyId)
            ->with(['user:id,name', 'followable']);

        // Role-based filtering
        if (!$user->isAdmin() && !$user->isManager()) {
            $query->where('user_id', $user->id);
        }

        // Filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        if ($request->has('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        if ($request->has('overdue') && $request->boolean('overdue')) {
            $query->overdue();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'scheduled_at');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $followUps = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $followUps->items(),
            'meta' => [
                'current_page' => $followUps->currentPage(),
                'last_page' => $followUps->lastPage(),
                'per_page' => $followUps->perPage(),
                'total' => $followUps->total(),
            ],
        ]);
    }

    /**
     * Get follow-ups for calendar view.
     */
    public function calendar(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = FollowUp::ofCompany($companyId)
            ->with(['user:id,name', 'followable'])
            ->betweenDates($request->start_date, $request->end_date);

        if (!$user->isAdmin() && !$user->isManager()) {
            $query->where('user_id', $user->id);
        }

        $followUps = $query->get()->map(function ($followUp) {
            return [
                'id' => $followUp->id,
                'title' => $followUp->purpose,
                'start' => $followUp->scheduled_at->toIso8601String(),
                'end' => $followUp->scheduled_at->addHour()->toIso8601String(),
                'color' => $followUp->priority_color,
                'status' => $followUp->status,
                'entity_name' => $followUp->entity_name,
                'entity_type' => class_basename($followUp->followable_type),
                'user' => $followUp->user?->name,
                'is_overdue' => $followUp->isOverdue(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $followUps,
        ]);
    }

    /**
     * Get today's follow-ups.
     */
    public function today(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $query = FollowUp::ofCompany($companyId)
            ->with(['user:id,name', 'followable'])
            ->today()
            ->pending()
            ->orderBy('scheduled_at');

        if (!$user->isAdmin() && !$user->isManager()) {
            $query->where('user_id', $user->id);
        }

        $followUps = $query->get();

        return response()->json([
            'success' => true,
            'data' => $followUps,
        ]);
    }

    /**
     * Get overdue follow-ups.
     */
    public function overdue(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $query = FollowUp::ofCompany($companyId)
            ->with(['user:id,name', 'followable'])
            ->overdue()
            ->orderBy('scheduled_at');

        if (!$user->isAdmin() && !$user->isManager()) {
            $query->where('user_id', $user->id);
        }

        $followUps = $query->limit(50)->get();

        return response()->json([
            'success' => true,
            'data' => $followUps,
            'count' => $followUps->count(),
        ]);
    }

    /**
     * Create a new follow-up.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'followable_type' => 'required|in:lead,client',
            'followable_id' => 'required|integer',
            'purpose' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'scheduled_at' => 'required|date|after_or_equal:now',
            'priority' => 'nullable|in:high,medium,low',
            'user_id' => 'nullable|exists:users,id',
            'remind_at' => 'nullable|date|before:scheduled_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate followable exists
        $followableClass = $request->followable_type === 'lead' ? Lead::class : Client::class;
        $followable = $followableClass::ofCompany($user->company_id)->find($request->followable_id);

        if (!$followable) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($request->followable_type) . ' not found',
            ], 404);
        }

        $followUp = FollowUp::create([
            'company_id' => $user->company_id,
            'user_id' => $request->user_id ?? $user->id,
            'followable_type' => $followableClass,
            'followable_id' => $request->followable_id,
            'purpose' => $request->purpose,
            'notes' => $request->notes,
            'scheduled_at' => $request->scheduled_at,
            'priority' => $request->priority ?? 'medium',
            'status' => 'pending',
            'remind_at' => $request->remind_at,
        ]);

        ActivityLog::log('created', "Scheduled follow-up for {$followable->name}: {$request->purpose}", $followUp);

        $followUp->load(['user:id,name', 'followable']);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up scheduled successfully',
            'data' => $followUp,
        ], 201);
    }

    /**
     * Get a single follow-up.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $followUp = FollowUp::ofCompany($user->company_id)
            ->with(['user:id,name,phone,email', 'followable'])
            ->find($id);

        if (!$followUp) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-up not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $followUp,
        ]);
    }

    /**
     * Update a follow-up.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $followUp = FollowUp::ofCompany($user->company_id)->find($id);

        if (!$followUp) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-up not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'purpose' => 'sometimes|required|string|max:255',
            'notes' => 'nullable|string',
            'scheduled_at' => 'sometimes|date',
            'priority' => 'nullable|in:high,medium,low',
            'user_id' => 'nullable|exists:users,id',
            'remind_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $followUp->update($request->only([
            'purpose', 'notes', 'scheduled_at', 'priority', 'user_id', 'remind_at'
        ]));

        ActivityLog::log('updated', "Updated follow-up: {$followUp->purpose}", $followUp);

        $followUp->load(['user:id,name', 'followable']);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up updated successfully',
            'data' => $followUp,
        ]);
    }

    /**
     * Mark follow-up as completed.
     */
    public function complete(Request $request, $id)
    {
        $user = $request->user();

        $followUp = FollowUp::ofCompany($user->company_id)->find($id);

        if (!$followUp) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-up not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'outcome' => 'nullable|string',
            'next_follow_up' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $followUp->markCompleted($request->outcome);

        // Create next follow-up if specified
        if ($request->has('next_follow_up')) {
            FollowUp::create([
                'company_id' => $followUp->company_id,
                'user_id' => $followUp->user_id,
                'followable_type' => $followUp->followable_type,
                'followable_id' => $followUp->followable_id,
                'purpose' => 'Follow-up',
                'scheduled_at' => $request->next_follow_up,
                'priority' => 'medium',
                'status' => 'pending',
            ]);
        }

        ActivityLog::log('completed', "Completed follow-up: {$followUp->purpose}", $followUp);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up marked as completed',
            'data' => $followUp,
        ]);
    }

    /**
     * Reschedule a follow-up.
     */
    public function reschedule(Request $request, $id)
    {
        $user = $request->user();

        $followUp = FollowUp::ofCompany($user->company_id)->find($id);

        if (!$followUp) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-up not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $followUp->reschedule($request->scheduled_at);

        ActivityLog::log('rescheduled', "Rescheduled follow-up: {$followUp->purpose}", $followUp);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up rescheduled',
            'data' => $followUp,
        ]);
    }

    /**
     * Cancel a follow-up.
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        $followUp = FollowUp::ofCompany($user->company_id)->find($id);

        if (!$followUp) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-up not found',
            ], 404);
        }

        $followUp->update(['status' => 'cancelled']);

        ActivityLog::log('cancelled', "Cancelled follow-up: {$followUp->purpose}", $followUp);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up cancelled',
        ]);
    }

    /**
     * Delete a follow-up.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $followUp = FollowUp::ofCompany($user->company_id)->find($id);

        if (!$followUp) {
            return response()->json([
                'success' => false,
                'message' => 'Follow-up not found',
            ], 404);
        }

        $followUp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Follow-up deleted successfully',
        ]);
    }
}
