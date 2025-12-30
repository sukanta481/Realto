<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'property_id',
        'handled_by',
        'buyer_id',
        'seller_id',
        'title',
        'type',
        'deal_value',
        'commission_percentage',
        'commission_amount',
        'commission_from',
        'stage',
        'expected_close_date',
        'closed_date',
        'agreement_date',
        'registration_date',
        'payment_status',
        'amount_received',
        'notes',
        'close_reason',
    ];

    protected $casts = [
        'deal_value' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'expected_close_date' => 'date',
        'closed_date' => 'date',
        'agreement_date' => 'date',
        'registration_date' => 'date',
    ];

    const STAGES = [
        'open' => 'Open',
        'negotiation' => 'Negotiation',
        'agreement' => 'Agreement',
        'documentation' => 'Documentation',
        'closed_won' => 'Closed Won',
        'closed_lost' => 'Closed Lost',
    ];

    /**
     * Get the company the deal belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the property for this deal.
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user handling this deal.
     */
    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Get the buyer client.
     */
    public function buyer()
    {
        return $this->belongsTo(Client::class, 'buyer_id');
    }

    /**
     * Get the seller client.
     */
    public function seller()
    {
        return $this->belongsTo(Client::class, 'seller_id');
    }

    /**
     * Scope to get deals of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get deals handled by a specific user.
     */
    public function scopeHandledBy($query, $userId)
    {
        return $query->where('handled_by', $userId);
    }

    /**
     * Scope to get open deals (not closed).
     */
    public function scopeOpen($query)
    {
        return $query->whereNotIn('stage', ['closed_won', 'closed_lost']);
    }

    /**
     * Scope to get won deals.
     */
    public function scopeWon($query)
    {
        return $query->where('stage', 'closed_won');
    }

    /**
     * Scope to get lost deals.
     */
    public function scopeLost($query)
    {
        return $query->where('stage', 'closed_lost');
    }

    /**
     * Scope to get deals closed in a specific month.
     */
    public function scopeClosedInMonth($query, $year, $month)
    {
        return $query->where('stage', 'closed_won')
                    ->whereYear('closed_date', $year)
                    ->whereMonth('closed_date', $month);
    }

    /**
     * Scope to get deals by stage.
     */
    public function scopeAtStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Calculate commission based on deal value and percentage.
     */
    public function calculateCommission(): void
    {
        if ($this->deal_value && $this->commission_percentage) {
            $this->commission_amount = ($this->deal_value * $this->commission_percentage) / 100;
        }
    }

    /**
     * Close the deal as won.
     */
    public function closeAsWon(array $data = []): void
    {
        $this->update(array_merge([
            'stage' => 'closed_won',
            'closed_date' => $data['closed_date'] ?? now(),
        ], $data));

        // Update property status
        if ($this->property) {
            $this->property->update([
                'status' => $this->type === 'sale' ? 'sold' : 'rented',
            ]);
        }
    }

    /**
     * Close the deal as lost.
     */
    public function closeAsLost(string $reason = null): void
    {
        $this->update([
            'stage' => 'closed_lost',
            'closed_date' => now(),
            'close_reason' => $reason,
        ]);
    }

    /**
     * Check if deal is closed.
     */
    public function isClosed(): bool
    {
        return in_array($this->stage, ['closed_won', 'closed_lost']);
    }

    /**
     * Check if deal is won.
     */
    public function isWon(): bool
    {
        return $this->stage === 'closed_won';
    }

    /**
     * Get formatted deal value.
     */
    public function getFormattedValueAttribute(): string
    {
        return $this->formatIndianCurrency($this->deal_value);
    }

    /**
     * Get formatted commission amount.
     */
    public function getFormattedCommissionAttribute(): string
    {
        return $this->formatIndianCurrency($this->commission_amount);
    }

    /**
     * Format currency in Indian format.
     */
    private function formatIndianCurrency($amount): string
    {
        if (!$amount) {
            return '₹0';
        }

        if ($amount >= 10000000) {
            return '₹' . round($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) {
            return '₹' . round($amount / 100000, 2) . ' L';
        }
        return '₹' . number_format($amount);
    }

    /**
     * Get stage display name.
     */
    public function getStageNameAttribute(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }

    /**
     * Get stage color for UI.
     */
    public function getStageColorAttribute(): string
    {
        return match($this->stage) {
            'open' => '#3B82F6',
            'negotiation' => '#8B5CF6',
            'agreement' => '#F59E0B',
            'documentation' => '#EC4899',
            'closed_won' => '#10B981',
            'closed_lost' => '#EF4444',
            default => '#6B7280',
        };
    }
}
