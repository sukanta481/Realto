<?php

namespace App\Exports;

use App\Models\Property;
use Illuminate\Support\Collection;

class PropertiesExport
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
        $query = Property::ofCompany($this->companyId)
            ->with(['propertyType:id,name', 'addedBy:id,name']);

        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['listing_type'])) {
            $query->where('listing_type', $this->filters['listing_type']);
        }

        if (!empty($this->filters['city'])) {
            $query->where('city', $this->filters['city']);
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
            'Title',
            'Type',
            'Listing Type',
            'Price',
            'BHK',
            'Area',
            'Location',
            'City',
            'Status',
            'Owner Name',
            'Owner Phone',
            'Added By',
            'Created At',
        ];
    }

    /**
     * Transform property to row data.
     */
    public function transformRow(Property $property): array
    {
        return [
            $property->id,
            $property->title,
            $property->propertyType?->name ?? 'N/A',
            ucfirst($property->listing_type ?? 'sale'),
            $property->formatted_price,
            $property->bhk ?? 'N/A',
            $property->display_area,
            $property->locality ?? 'N/A',
            $property->city ?? 'N/A',
            ucfirst($property->status ?? 'available'),
            $property->owner_name ?? 'N/A',
            $property->owner_phone ?? 'N/A',
            $property->addedBy?->name ?? 'N/A',
            $property->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Get all rows for export.
     */
    public function getRows(): array
    {
        $data = $this->getData();
        $rows = [$this->getHeaders()];

        foreach ($data as $property) {
            $rows[] = $this->transformRow($property);
        }

        return $rows;
    }

    /**
     * Get filename for export.
     */
    public function getFilename(): string
    {
        return 'properties_' . date('Y-m-d_His');
    }
}
