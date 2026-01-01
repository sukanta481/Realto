<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'permissions',
        'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    /**
     * Get the users with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if the role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        // Admin has all permissions
        if (in_array('*', $this->permissions)) {
            return true;
        }

        // Check for exact match
        if (in_array($permission, $this->permissions)) {
            return true;
        }

        // Check for wildcard match (e.g., leads.* matches leads.create)
        $permissionParts = explode('.', $permission);
        if (count($permissionParts) === 2) {
            $wildcardPermission = $permissionParts[0] . '.*';
            if (in_array($wildcardPermission, $this->permissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this is the admin role.
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    /**
     * Scope to get roles of a specific company.
     */
    public function scopeOfCompany($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)
              ->orWhere('is_system', true);
        });
    }

    /**
     * Get all available permissions.
     */
    public static function getAvailablePermissions(): array
    {
        return [
            'leads' => [
                'leads.view' => 'View leads',
                'leads.create' => 'Create leads',
                'leads.edit' => 'Edit leads',
                'leads.delete' => 'Delete leads',
                'leads.export' => 'Export leads',
                'leads.assign' => 'Assign leads to team members',
            ],
            'properties' => [
                'properties.view' => 'View properties',
                'properties.create' => 'Add properties',
                'properties.edit' => 'Edit properties',
                'properties.delete' => 'Delete properties',
                'properties.export' => 'Export properties',
            ],
            'deals' => [
                'deals.view' => 'View deals',
                'deals.create' => 'Create deals',
                'deals.edit' => 'Edit deals',
                'deals.close' => 'Close deals',
                'deals.delete' => 'Delete deals',
            ],
            'follow_ups' => [
                'follow_ups.view' => 'View follow-ups',
                'follow_ups.create' => 'Create follow-ups',
                'follow_ups.complete' => 'Complete follow-ups',
                'follow_ups.delete' => 'Delete follow-ups',
            ],
            'clients' => [
                'clients.view' => 'View clients',
                'clients.create' => 'Create clients',
                'clients.edit' => 'Edit clients',
                'clients.delete' => 'Delete clients',
            ],
            'team' => [
                'team.view' => 'View team members',
                'team.manage' => 'Manage team members',
                'team.invite' => 'Invite new members',
            ],
            'reports' => [
                'reports.view' => 'View reports',
                'reports.export' => 'Export reports',
            ],
            'settings' => [
                'settings.view' => 'View settings',
                'settings.edit' => 'Edit settings',
            ],
        ];
    }

    /**
     * Get default permissions for a role type.
     */
    public static function getDefaultPermissions(string $roleType): array
    {
        return match ($roleType) {
            'admin' => ['*'], // All permissions
            'manager' => [
                'leads.*', 'properties.*', 'deals.*', 'follow_ups.*', 
                'clients.*', 'team.view', 'reports.*', 'settings.view',
            ],
            'agent' => [
                'leads.view', 'leads.create', 'leads.edit',
                'properties.view',
                'deals.view', 'deals.create',
                'follow_ups.*',
                'clients.view', 'clients.create',
            ],
            default => ['leads.view', 'properties.view', 'follow_ups.view'],
        };
    }
}

