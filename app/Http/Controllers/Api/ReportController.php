<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Deal;
use App\Models\FollowUp;
use App\Models\User;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get lead analytics.
     */
    public function leadAnalytics(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $days = $request->get('days', 30);
        
        $startDate = now()->subDays($days);
        $endDate = now();

        // Lead trend (daily counts)
        $leadTrend = Lead::ofCompany($companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Lead sources
        $leadSources = Lead::ofCompany($companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        // Lead status distribution
        $leadStatuses = Lead::ofCompany($companyId)
            ->with('status:id,name,color')
            ->selectRaw('lead_status_id, COUNT(*) as count')
            ->groupBy('lead_status_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->status?->name ?? 'Unknown',
                    'color' => $item->status?->color ?? '#94a3b8',
                    'count' => $item->count,
                ];
            });

        // Conversion funnel
        $totalLeads = Lead::ofCompany($companyId)->count();
        $contactedLeads = Lead::ofCompany($companyId)->whereNotNull('last_contacted_at')->count();
        $qualifiedLeads = Lead::ofCompany($companyId)->whereIn('lead_status_id', 
            LeadStatus::ofCompany($companyId)->where('name', 'like', '%qualified%')->pluck('id')
        )->count();
        $convertedLeads = Lead::ofCompany($companyId)->whereNotNull('converted_at')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'trend' => $this->fillDateGaps($leadTrend, $startDate, $endDate),
                'sources' => $leadSources,
                'statuses' => $leadStatuses,
                'funnel' => [
                    ['name' => 'Total Leads', 'value' => $totalLeads],
                    ['name' => 'Contacted', 'value' => $contactedLeads],
                    ['name' => 'Qualified', 'value' => $qualifiedLeads],
                    ['name' => 'Converted', 'value' => $convertedLeads],
                ],
            ],
        ]);
    }

    /**
     * Get revenue analytics.
     */
    public function revenueAnalytics(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $months = $request->get('months', 6);

        $revenueByMonth = [];
        $dealsByMonth = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            $monthLabel = $date->format('M Y');

            $monthStats = Deal::ofCompany($companyId)
                ->where('stage', 'closed_won')
                ->whereBetween('closed_date', [$startOfMonth, $endOfMonth])
                ->selectRaw('SUM(commission_amount) as revenue, COUNT(*) as deals')
                ->first();

            $revenueByMonth[$monthLabel] = (float) ($monthStats->revenue ?? 0);
            $dealsByMonth[$monthLabel] = (int) ($monthStats->deals ?? 0);
        }

        // Deal stage distribution
        $dealStages = Deal::ofCompany($companyId)
            ->selectRaw('stage, COUNT(*) as count')
            ->groupBy('stage')
            ->pluck('count', 'stage')
            ->toArray();

        // Top performers
        $topPerformers = User::where('company_id', $companyId)
            ->whereHas('handledDeals', function ($q) {
                $q->where('stage', 'closed_won');
            })
            ->withCount(['handledDeals as closed_deals' => function ($q) {
                $q->where('stage', 'closed_won');
            }])
            ->withSum(['handledDeals as total_revenue' => function ($q) {
                $q->where('stage', 'closed_won');
            }], 'commission_amount')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'deals' => $user->closed_deals,
                    'revenue' => $user->total_revenue ?? 0,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'revenue_trend' => $revenueByMonth,
                'deals_trend' => $dealsByMonth,
                'deal_stages' => $dealStages,
                'top_performers' => $topPerformers,
                'totals' => [
                    'revenue' => array_sum($revenueByMonth),
                    'deals' => array_sum($dealsByMonth),
                ],
            ],
        ]);
    }

    /**
     * Get property analytics.
     */
    public function propertyAnalytics(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        // Status distribution
        $statusDistribution = Property::ofCompany($companyId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Listing type distribution
        $listingType = Property::ofCompany($companyId)
            ->selectRaw('listing_type, COUNT(*) as count')
            ->groupBy('listing_type')
            ->pluck('count', 'listing_type')
            ->toArray();

        // By city
        $byCity = Property::ofCompany($companyId)
            ->selectRaw('city, COUNT(*) as count')
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'city')
            ->toArray();

        // Price ranges
        $priceRanges = [
            '0-25L' => Property::ofCompany($companyId)->where('price', '<', 2500000)->count(),
            '25L-50L' => Property::ofCompany($companyId)->whereBetween('price', [2500000, 5000000])->count(),
            '50L-1Cr' => Property::ofCompany($companyId)->whereBetween('price', [5000000, 10000000])->count(),
            '1Cr-2Cr' => Property::ofCompany($companyId)->whereBetween('price', [10000000, 20000000])->count(),
            '2Cr+' => Property::ofCompany($companyId)->where('price', '>', 20000000)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'status_distribution' => $statusDistribution,
                'listing_type' => $listingType,
                'by_city' => $byCity,
                'price_ranges' => $priceRanges,
            ],
        ]);
    }

    /**
     * Get team performance analytics.
     */
    public function teamPerformance(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $days = $request->get('days', 30);

        $startDate = now()->subDays($days);

        $teamStats = User::where('company_id', $companyId)
            ->withCount([
                'assignedLeads as leads_count' => function ($q) use ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                },
                'followUps as followups_count' => function ($q) use ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                },
                'followUps as completed_followups' => function ($q) use ($startDate) {
                    $q->where('status', 'completed')->where('updated_at', '>=', $startDate);
                },
            ])
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'role' => $member->role?->display_name ?? 'Agent',
                    'leads' => $member->leads_count,
                    'followups' => $member->followups_count,
                    'completed' => $member->completed_followups,
                    'completion_rate' => $member->followups_count > 0 
                        ? round(($member->completed_followups / $member->followups_count) * 100) 
                        : 0,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $teamStats,
        ]);
    }

    /**
     * Fill date gaps in trend data.
     */
    protected function fillDateGaps(array $data, $startDate, $endDate): array
    {
        $filled = [];
        $current = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        while ($current <= $end) {
            $dateKey = $current->format('Y-m-d');
            $filled[$dateKey] = $data[$dateKey] ?? 0;
            $current->addDay();
        }

        return $filled;
    }
}
