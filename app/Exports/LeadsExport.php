<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Collection;

class LeadsExport
{
    protected $companyId;
    protected $filters;

    public function __construct($companyId, array $filters = [])
    {
        $this->companyId = $companyId;
        $this->filters = $filters;
    }

    /**
     * Get data for export.
     */
    public function getData(): Collection
    {
        $query = Lead::ofCompany($this->companyId)
            ->with(['status:id,name', 'assignedTo:id,name']);

        // Apply filters
        if (!empty($this->filters['status_id'])) {
            $query->where('lead_status_id', $this->filters['status_id']);
        }

        if (!empty($this->filters['assigned_to'])) {
            $query->where('assigned_to', $this->filters['assigned_to']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get headers for export.
     */
    public function getHeaders(): array
    {
        return [
            'ID',
            'Name',
            'Phone',
            'Email',
            'Status',
            'Budget Range',
            'Location Preference',
            'Property Type',
            'Source',
            'Priority',
            'Assigned To',
            'Created At',
        ];
    }

    /**
     * Transform lead to row data.
     */
    public function transformRow(Lead $lead): array
    {
        return [
            $lead->id,
            $lead->name,
            $lead->phone,
            $lead->email ?? 'N/A',
            $lead->status?->name ?? 'N/A',
            $lead->budget_range ?? 'N/A',
            $lead->location_preference ?? 'N/A',
            $lead->property_type ?? 'N/A',
            $lead->source ?? 'N/A',
            $this->getPriorityLabel($lead->priority),
            $lead->assignedTo?->name ?? 'Unassigned',
            $lead->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Get all rows for export.
     */
    public function getRows(): array
    {
        $data = $this->getData();
        $rows = [$this->getHeaders()];

        foreach ($data as $lead) {
            $rows[] = $this->transformRow($lead);
        }

        return $rows;
    }

    /**
     * Get priority label.
     */
    protected function getPriorityLabel($priority): string
    {
        return match ($priority) {
            1 => 'High',
            2 => 'Medium',
            3 => 'Low',
            default => 'Medium',
        };
    }

    /**
     * Get filename for export.
     */
    public function getFilename(): string
    {
        return 'leads_' . date('Y-m-d_His');
    }
}
