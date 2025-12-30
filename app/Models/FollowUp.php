<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'followable_type',
        'followable_id',
        'purpose',
        'notes',
        'scheduled_at',
        'completed_at',
        'priority',
        'status',
        'outcome',
        'next_follow_up',
        'reminder_sent',
        'remind_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_follow_up' => 'datetime',
        'remind_at' => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    /**
     * Get the company the follow-up belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user assigned to the follow-up.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model (Lead or Client).
     */
    public function followable()
    {
        return $this->morphTo();
    }

    /**
     * Scope to get follow-ups of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get follow-ups for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get pending follow-ups.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get today's follow-ups.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Scope to get overdue follow-ups.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('scheduled_at', '<', now());
    }

    /**
     * Scope to get upcoming follow-ups (next 7 days).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'pending')
                    ->whereBetween('scheduled_at', [now(), now()->addDays(7)]);
    }

    /**
     * Scope to get high priority follow-ups.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope to get follow-ups for a date range.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('scheduled_at', [$start, $end]);
    }

    /**
     * Mark the follow-up as completed.
     */
    public function markCompleted(string $outcome = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'outcome' => $outcome,
        ]);
    }

    /**
     * Reschedule the follow-up.
     */
    public function reschedule($newDateTime): void
    {
        $this->update([
            'status' => 'rescheduled',
            'scheduled_at' => $newDateTime,
        ]);
    }

    /**
     * Check if the follow-up is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->scheduled_at < now();
    }

    /**
     * Get the display name of the related entity.
     */
    public function getEntityNameAttribute(): string
    {
        return $this->followable?->name ?? 'Unknown';
    }

    /**
     * Get the priority color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => '#EF4444',
            'medium' => '#F59E0B',
            'low' => '#10B981',
            default => '#6B7280',
        };
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => $this->isOverdue() ? '#EF4444' : '#3B82F6',
            'completed' => '#10B981',
            'cancelled' => '#6B7280',
            'rescheduled' => '#F59E0B',
            default => '#6B7280',
        };
    }
}
