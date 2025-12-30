<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'assigned_to',
        'lead_status_id',
        'name',
        'phone',
        'email',
        'alternate_phone',
        'budget_min',
        'budget_max',
        'location_preference',
        'preferred_locations',
        'property_type',
        'purpose',
        'bhk',
        'area_min',
        'area_max',
        'source',
        'source_details',
        'priority',
        'notes',
        'converted_client_id',
        'converted_at',
    ];

    protected $casts = [
        'preferred_locations' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'area_min' => 'decimal:2',
        'area_max' => 'decimal:2',
        'converted_at' => 'datetime',
    ];

    /**
     * Get the company the lead belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user assigned to the lead.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the status of the lead.
     */
    public function status()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    /**
     * Get the follow-ups for the lead.
     */
    public function followUps()
    {
        return $this->morphMany(FollowUp::class, 'followable');
    }

    /**
     * Get the client the lead was converted to.
     */
    public function convertedClient()
    {
        return $this->belongsTo(Client::class, 'converted_client_id');
    }

    /**
     * Get the property visits by this lead.
     */
    public function propertyVisits()
    {
        return $this->hasMany(PropertyVisit::class);
    }

    /**
     * Scope to get leads of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get leads assigned to a specific user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope to get leads by status.
     */
    public function scopeWithStatus($query, $statusId)
    {
        return $query->where('lead_status_id', $statusId);
    }

    /**
     * Scope to get leads created today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to get high priority leads.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 1);
    }

    /**
     * Check if lead is converted.
     */
    public function isConverted(): bool
    {
        return $this->converted_client_id !== null;
    }

    /**
     * Get formatted budget range.
     */
    public function getBudgetRangeAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return $this->formatIndianCurrency($this->budget_min) . ' - ' . $this->formatIndianCurrency($this->budget_max);
        } elseif ($this->budget_max) {
            return 'Up to ' . $this->formatIndianCurrency($this->budget_max);
        } elseif ($this->budget_min) {
            return 'From ' . $this->formatIndianCurrency($this->budget_min);
        }
        return 'Not specified';
    }

    /**
     * Format currency in Indian format.
     */
    private function formatIndianCurrency($amount): string
    {
        if ($amount >= 10000000) {
            return '₹' . round($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) {
            return '₹' . round($amount / 100000, 2) . ' L';
        }
        return '₹' . number_format($amount);
    }

    /**
     * Get WhatsApp link for the lead.
     */
    public function getWhatsAppLinkAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }
        return 'https://wa.me/' . $phone;
    }

    /**
     * Get phone link for calling.
     */
    public function getPhoneLinkAttribute(): string
    {
        return 'tel:' . $this->phone;
    }
}
