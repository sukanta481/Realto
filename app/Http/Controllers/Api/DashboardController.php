<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;
use App\Models\FollowUp;
use App\Models\Deal;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        // Get date ranges
        $today = today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // Base query modifier based on role
        $userFilter = function ($query) use ($user) {
            if (!$user->isAdmin() && !$user->isManager()) {
                $query->where('assigned_to', $user->id);
            }
        };

        // KPI Cards
        $stats = [
            'leads_today' => Lead::ofCompany($companyId)
                ->where(function ($q) use ($userFilter) { $userFilter($q); })
                ->whereDate('created_at', $today)
                ->count(),
            
            'follow_ups_today' => FollowUp::ofCompany($companyId)
                ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereDate('scheduled_at', $today)
                ->where('status', 'pending')
                ->count(),
            
            'active_properties' => Property::ofCompany($companyId)
                ->where('status', 'available')
                ->where('is_active', true)
                ->count(),
            
            'deals_this_month' => Deal::ofCompany($companyId)
                ->where(function ($q) use ($userFilter, $user) {
                    if (!$user->isAdmin() && !$user->isManager()) {
                        $q->where('handled_by', $user->id);
                    }
                })
                ->where('stage', 'closed_won')
                ->whereBetween('closed_date', [$startOfMonth, $endOfMonth])
                ->count(),
            
            'revenue_this_month' => Deal::ofCompany($companyId)
                ->where(function ($q) use ($user) {
                    if (!$user->isAdmin() && !$user->isManager()) {
                        $q->where('handled_by', $user->id);
                    }
                })
                ->where('stage', 'closed_won')
                ->whereBetween('closed_date', [$startOfMonth, $endOfMonth])
                ->sum('commission_amount'),
        ];

        // Lead Funnel
        $leadFunnel = $this->getLeadFunnel($companyId, $user);

        // Today's Tasks
        $todaysTasks = FollowUp::ofCompany($companyId)
            ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['followable', 'user:id,name'])
            ->whereDate('scheduled_at', $today)
            ->where('status', 'pending')
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'purpose' => $task->purpose,
                    'scheduled_at' => $task->scheduled_at->format('H:i'),
                    'priority' => $task->priority,
                    'priority_color' => $task->priority_color,
                    'entity_name' => $task->entity_name,
                    'entity_type' => class_basename($task->followable_type),
                    'assigned_to' => $task->user?->name,
                ];
            });

        // Overdue Tasks Count
        $overdueCount = FollowUp::ofCompany($companyId)
            ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->overdue()
            ->count();

        // Recent Activity
        $recentActivity = ActivityLog::ofCompany($companyId)
            ->with('user:id,name')
            ->recent(15)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'user' => $log->user?->name,
                    'icon' => $log->action_icon,
                    'color' => $log->action_color,
                    'time' => $log->created_at->diffForHumans(),
                ];
            });

        // Open Deals
        $openDeals = Deal::ofCompany($companyId)
            ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                $q->where('handled_by', $user->id);
            })
            ->open()
            ->with(['property:id,title', 'buyer:id,name'])
            ->orderBy('expected_close_date')
            ->limit(5)
            ->get()
            ->map(function ($deal) {
                return [
                    'id' => $deal->id,
                    'title' => $deal->title,
                    'value' => $deal->formatted_value,
                    'stage' => $deal->stage_name,
                    'stage_color' => $deal->stage_color,
                    'property' => $deal->property?->title,
                    'buyer' => $deal->buyer?->name,
                    'expected_close' => $deal->expected_close_date?->format('d M'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'lead_funnel' => $leadFunnel,
                'todays_tasks' => $todaysTasks,
                'overdue_count' => $overdueCount,
                'recent_activity' => $recentActivity,
                'open_deals' => $openDeals,
            ],
        ]);
    }

    /**
     * Get lead funnel data.
     */
    private function getLeadFunnel($companyId, $user)
    {
        $query = Lead::ofCompany($companyId)
            ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })
            ->select('lead_status_id', DB::raw('count(*) as count'))
            ->groupBy('lead_status_id');

        $leadCounts = $query->pluck('count', 'lead_status_id');

        $statuses = $user->company->leadStatuses()->get();

        return $statuses->map(function ($status) use ($leadCounts) {
            return [
                'id' => $status->id,
                'name' => $status->name,
                'color' => $status->color,
                'count' => $leadCounts[$status->id] ?? 0,
            ];
        });
    }

    /**
     * Get quick stats for mobile.
     */
    public function quickStats(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $today = today();

        $stats = [
            'pending_follow_ups' => FollowUp::ofCompany($companyId)
                ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where('status', 'pending')
                ->where('scheduled_at', '<=', now())
                ->count(),
            
            'new_leads_today' => Lead::ofCompany($companyId)
                ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                    $q->where('assigned_to', $user->id);
                })
                ->whereDate('created_at', $today)
                ->count(),
            
            'tasks_today' => FollowUp::ofCompany($companyId)
                ->when(!$user->isAdmin() && !$user->isManager(), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereDate('scheduled_at', $today)
                ->where('status', 'pending')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
