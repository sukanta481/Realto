<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\LeadsExport;
use App\Exports\PropertiesExport;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Deal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export leads.
     */
    public function exportLeads(Request $request)
    {
        $user = $request->user();
        $format = $request->get('format', 'xlsx');
        $filters = $request->only(['status_id', 'assigned_to', 'date_from', 'date_to']);

        $export = new LeadsExport($user->company_id, $filters);

        if ($format === 'pdf') {
            return $this->exportToPdf($export, 'Leads Report', 'exports.leads');
        }

        return $this->exportToCsv($export);
    }

    /**
     * Export properties.
     */
    public function exportProperties(Request $request)
    {
        $user = $request->user();
        $format = $request->get('format', 'xlsx');
        $filters = $request->only(['status', 'listing_type', 'city', 'date_from', 'date_to']);

        $export = new PropertiesExport($user->company_id, $filters);

        if ($format === 'pdf') {
            return $this->exportToPdf($export, 'Properties Report', 'exports.properties');
        }

        return $this->exportToCsv($export);
    }

    /**
     * Export deals.
     */
    public function exportDeals(Request $request)
    {
        $user = $request->user();
        $format = $request->get('format', 'xlsx');
        $companyId = $user->company_id;

        $query = Deal::ofCompany($companyId)
            ->with(['property:id,title', 'buyer:id,name', 'handledBy:id,name']);

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $deals = $query->orderBy('created_at', 'desc')->get();

        $rows = [['ID', 'Title', 'Property', 'Buyer', 'Value', 'Commission', 'Stage', 'Handled By', 'Created At']];

        foreach ($deals as $deal) {
            $rows[] = [
                $deal->id,
                $deal->title ?? 'Deal #' . $deal->id,
                $deal->property?->title ?? 'N/A',
                $deal->buyer?->name ?? 'N/A',
                $deal->formatted_value ?? 'N/A',
                '₹' . number_format($deal->commission_amount ?? 0),
                $deal->stage_name ?? 'N/A',
                $deal->handledBy?->name ?? 'N/A',
                $deal->created_at->format('d/m/Y'),
            ];
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.deals', [
                'title' => 'Deals Report',
                'data' => $deals,
                'generatedAt' => now()->format('d M Y, h:i A'),
            ]);
            return $pdf->download('deals_' . date('Y-m-d_His') . '.pdf');
        }

        return $this->arrayToCsv($rows, 'deals_' . date('Y-m-d_His'));
    }

    /**
     * Export monthly summary report.
     */
    public function exportMonthlySummary(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $month = $request->get('month', now()->format('Y-m'));

        $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Gather stats
        $stats = [
            'period' => $startDate->format('F Y'),
            'new_leads' => Lead::ofCompany($companyId)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'converted_leads' => Lead::ofCompany($companyId)->whereBetween('converted_at', [$startDate, $endDate])->count(),
            'new_properties' => Property::ofCompany($companyId)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'deals_closed' => Deal::ofCompany($companyId)->where('stage', 'closed_won')->whereBetween('closed_date', [$startDate, $endDate])->count(),
            'revenue' => Deal::ofCompany($companyId)->where('stage', 'closed_won')->whereBetween('closed_date', [$startDate, $endDate])->sum('commission_amount'),
        ];

        $pdf = Pdf::loadView('exports.monthly-summary', [
            'stats' => $stats,
            'companyName' => $user->company?->name ?? 'Company',
            'generatedAt' => now()->format('d M Y, h:i A'),
        ]);

        return $pdf->download('monthly_summary_' . $month . '.pdf');
    }

    /**
     * Export to CSV helper.
     */
    protected function exportToCsv($export)
    {
        $rows = $export->getRows();
        return $this->arrayToCsv($rows, $export->getFilename());
    }

    /**
     * Convert array to CSV response.
     */
    protected function arrayToCsv(array $rows, string $filename)
    {
        $output = fopen('php://temp', 'r+');
        
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ]);
    }

    /**
     * Export to PDF helper.
     */
    protected function exportToPdf($export, string $title, string $view)
    {
        $pdf = Pdf::loadView($view, [
            'title' => $title,
            'headers' => $export->getHeaders(),
            'data' => $export->getData(),
            'generatedAt' => now()->format('d M Y, h:i A'),
        ]);

        return $pdf->download($export->getFilename() . '.pdf');
    }
}
