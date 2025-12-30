<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'city',
        'state',
        'address',
        'logo',
        'settings',
        'operating_areas',
        'is_active',
        'onboarding_completed_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'operating_areas' => 'array',
        'is_active' => 'boolean',
        'onboarding_completed_at' => 'datetime',
    ];

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the leads for the company.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the properties for the company.
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Get the clients for the company.
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the deals for the company.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Get the lead statuses for the company.
     */
    public function leadStatuses()
    {
        return $this->hasMany(LeadStatus::class)->orderBy('order');
    }

    /**
     * Get the property types for the company.
     */
    public function propertyTypes()
    {
        return $this->hasMany(PropertyType::class);
    }

    /**
     * Get the activity logs for the company.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Create default lead statuses for a new company.
     */
    public function createDefaultLeadStatuses()
    {
        $statuses = [
            ['name' => 'New', 'color' => '#3B82F6', 'order' => 1, 'is_default' => true],
            ['name' => 'Contacted', 'color' => '#8B5CF6', 'order' => 2],
            ['name' => 'Qualified', 'color' => '#F59E0B', 'order' => 3],
            ['name' => 'Site Visit', 'color' => '#10B981', 'order' => 4],
            ['name' => 'Negotiation', 'color' => '#EC4899', 'order' => 5],
            ['name' => 'Won', 'color' => '#22C55E', 'order' => 6, 'is_won' => true],
            ['name' => 'Lost', 'color' => '#EF4444', 'order' => 7, 'is_lost' => true],
        ];

        foreach ($statuses as $status) {
            $this->leadStatuses()->create($status);
        }
    }

    /**
     * Create default property types for a new company.
     */
    public function createDefaultPropertyTypes()
    {
        $types = [
            ['name' => 'Flat/Apartment'],
            ['name' => 'Independent House/Villa'],
            ['name' => 'Plot/Land'],
            ['name' => 'Commercial Shop'],
            ['name' => 'Commercial Office'],
            ['name' => 'Warehouse/Godown'],
            ['name' => 'Agricultural Land'],
        ];

        foreach ($types as $type) {
            $this->propertyTypes()->create($type);
        }
    }
}
