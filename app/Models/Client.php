<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'lead_id',
        'assigned_to',
        'name',
        'phone',
        'email',
        'alternate_phone',
        'address',
        'city',
        'occupation',
        'dob',
        'anniversary',
        'type',
        'status',
        'notes',
    ];

    protected $casts = [
        'dob' => 'date',
        'anniversary' => 'date',
    ];

    /**
     * Get the company the client belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the lead this client was converted from.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the user assigned to the client.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the follow-ups for the client.
     */
    public function followUps()
    {
        return $this->morphMany(FollowUp::class, 'followable');
    }

    /**
     * Get the deals where client is buyer.
     */
    public function dealsAsBuyer()
    {
        return $this->hasMany(Deal::class, 'buyer_id');
    }

    /**
     * Get the deals where client is seller.
     */
    public function dealsAsSeller()
    {
        return $this->hasMany(Deal::class, 'seller_id');
    }

    /**
     * Get all deals (buyer + seller).
     */
    public function getAllDealsAttribute()
    {
        return $this->dealsAsBuyer->merge($this->dealsAsSeller);
    }

    /**
     * Get the property visits.
     */
    public function propertyVisits()
    {
        return $this->hasMany(PropertyVisit::class);
    }

    /**
     * Scope to get clients of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get buyers.
     */
    public function scopeBuyers($query)
    {
        return $query->whereIn('type', ['buyer', 'both']);
    }

    /**
     * Scope to get sellers.
     */
    public function scopeSellers($query)
    {
        return $query->whereIn('type', ['seller', 'both']);
    }

    /**
     * Get WhatsApp link for the client.
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

    /**
     * Create a client from a lead.
     */
    public static function createFromLead(Lead $lead, array $additionalData = []): Client
    {
        $client = static::create(array_merge([
            'company_id' => $lead->company_id,
            'lead_id' => $lead->id,
            'assigned_to' => $lead->assigned_to,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'alternate_phone' => $lead->alternate_phone,
            'type' => 'buyer',
        ], $additionalData));

        // Update lead with conversion info
        $lead->update([
            'converted_client_id' => $client->id,
            'converted_at' => now(),
        ]);

        return $client;
    }
}
