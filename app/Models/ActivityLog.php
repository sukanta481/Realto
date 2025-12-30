<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the company the log belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Scope to get logs of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Log an activity.
     */
    public static function log(
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = []
    ): ?ActivityLog {
        $user = auth()->user();
        
        if (!$user || !$user->company_id) {
            return null;
        }

        return static::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get the action icon for UI.
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'created' => '➕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            'status_changed' => '🔄',
            'assigned' => '👤',
            'converted' => '✅',
            'closed_won' => '🎉',
            'closed_lost' => '❌',
            default => '📝',
        };
    }

    /**
     * Get the action color for UI.
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => '#10B981',
            'updated' => '#3B82F6',
            'deleted' => '#EF4444',
            'status_changed' => '#F59E0B',
            'assigned' => '#8B5CF6',
            'converted' => '#10B981',
            'closed_won' => '#22C55E',
            'closed_lost' => '#EF4444',
            default => '#6B7280',
        };
    }
}
