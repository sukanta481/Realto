<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'role_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'is_active',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'preferences' => 'array',
    ];

    /**
     * Get the company the user belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the role of the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the leads assigned to the user.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    /**
     * Get the follow-ups assigned to the user.
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get the deals handled by the user.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'handled_by');
    }

    /**
     * Get the properties added by the user.
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'added_by');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->isAdmin();
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return $this->role && $this->role->name === 'manager';
    }

    /**
     * Check if user can access a specific resource.
     * Admins and Managers can access all, others only their own.
     */
    public function canAccessResource($model): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        // Check if the model belongs to the user
        if (isset($model->assigned_to)) {
            return $model->assigned_to === $this->id;
        }

        if (isset($model->user_id)) {
            return $model->user_id === $this->id;
        }

        if (isset($model->handled_by)) {
            return $model->handled_by === $this->id;
        }

        return false;
    }

    /**
     * Scope to get users of the same company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get active users only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
