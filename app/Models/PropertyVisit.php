<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'property_id',
        'lead_id',
        'client_id',
        'conducted_by',
        'visit_date',
        'status',
        'feedback',
        'interest_level',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    /**
     * Get the company the visit belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the property being visited.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the lead visiting.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the client visiting.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who conducted the visit.
     */
    public function conductedBy()
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    /**
     * Scope to get visits of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get scheduled visits.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope to get today's visits.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', today());
    }

    /**
     * Scope to get upcoming visits.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('visit_date', '>=', now());
    }

    /**
     * Get the visitor name (lead or client).
     */
    public function getVisitorNameAttribute(): string
    {
        return $this->client?->name ?? $this->lead?->name ?? 'Unknown';
    }

    /**
     * Get the interest level display.
     */
    public function getInterestDisplayAttribute(): string
    {
        return match($this->interest_level) {
            5 => '🔥 Very High',
            4 => '⭐ High',
            3 => '👍 Medium',
            2 => '👎 Low',
            1 => '❌ Not Interested',
            default => 'Not Rated',
        };
    }
}
