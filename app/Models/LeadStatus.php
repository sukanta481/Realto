<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'color',
        'order',
        'is_default',
        'is_won',
        'is_lost',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_won' => 'boolean',
        'is_lost' => 'boolean',
    ];

    /**
     * Get the company the status belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the leads with this status.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Scope to get statuses of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Check if this is a closed status (won or lost).
     */
    public function isClosed(): bool
    {
        return $this->is_won || $this->is_lost;
    }
}
