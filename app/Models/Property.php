<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'added_by',
        'property_type_id',
        'title',
        'description',
        'listing_type',
        'address',
        'locality',
        'city',
        'state',
        'pincode',
        'latitude',
        'longitude',
        'bhk',
        'bedrooms',
        'bathrooms',
        'balconies',
        'carpet_area',
        'built_up_area',
        'super_built_up_area',
        'area_unit',
        'floor',
        'total_floors',
        'facing',
        'age_of_property',
        'price',
        'price_per_sqft',
        'price_negotiable',
        'maintenance',
        'security_deposit',
        'amenities',
        'furnishing_details',
        'furnishing',
        'status',
        'availability',
        'possession_date',
        'images',
        'video_url',
        'owner_name',
        'owner_phone',
        'source',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'furnishing_details' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'price_per_sqft' => 'decimal:2',
        'maintenance' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'carpet_area' => 'decimal:2',
        'built_up_area' => 'decimal:2',
        'super_built_up_area' => 'decimal:2',
        'price_negotiable' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'possession_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the company the property belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who added the property.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get the property type.
     */
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    /**
     * Get the deals for this property.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Get the property visits.
     */
    public function visits()
    {
        return $this->hasMany(PropertyVisit::class);
    }

    /**
     * Get the property images.
     */
    public function propertyImages()
    {
        return $this->hasMany(PropertyImage::class)->ordered();
    }

    /**
     * Get the primary image from property_images table.
     */
    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    /**
     * Scope to get properties of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get available properties.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to get sale properties.
     */
    public function scopeForSale($query)
    {
        return $query->where('listing_type', 'sale');
    }

    /**
     * Scope to get rental properties.
     */
    public function scopeForRent($query)
    {
        return $query->where('listing_type', 'rent');
    }

    /**
     * Scope to get active properties.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured properties.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by price range.
     */
    public function scopePriceBetween($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope to filter by city.
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Get formatted price in Indian currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->formatIndianCurrency($this->price);
    }

    /**
     * Format currency in Indian format.
     */
    private function formatIndianCurrency($amount): string
    {
        if (!$amount) {
            return 'Price on Request';
        }

        if ($amount >= 10000000) {
            return '₹' . round($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) {
            return '₹' . round($amount / 100000, 2) . ' L';
        }
        return '₹' . number_format($amount);
    }

    /**
     * Get the primary image or a placeholder.
     */
    public function getPrimaryImageAttribute(): ?string
    {
        if ($this->images && count($this->images) > 0) {
            return $this->images[0];
        }
        return null;
    }

    /**
     * Get the display area.
     */
    public function getDisplayAreaAttribute(): string
    {
        $area = $this->super_built_up_area ?? $this->built_up_area ?? $this->carpet_area;
        if ($area) {
            return number_format($area) . ' ' . $this->area_unit;
        }
        return 'N/A';
    }

    /**
     * Get the full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->locality,
            $this->city,
            $this->state,
            $this->pincode,
        ]);
        return implode(', ', $parts);
    }

    /**
     * Check if property is sold or rented.
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['sold', 'rented']);
    }

    /**
     * Get matching leads based on property criteria.
     */
    public function getMatchingLeads()
    {
        return Lead::ofCompany($this->company_id)
            ->where(function ($query) {
                // Match by budget
                if ($this->price) {
                    $query->where(function ($q) {
                        $q->whereNull('budget_max')
                          ->orWhere('budget_max', '>=', $this->price);
                    });
                }
            })
            ->where(function ($query) {
                // Match by location
                if ($this->city) {
                    $query->where('location_preference', 'like', '%' . $this->city . '%')
                          ->orWhereJsonContains('preferred_locations', $this->city);
                }
            })
            ->whereNull('converted_at')
            ->get();
    }
}
