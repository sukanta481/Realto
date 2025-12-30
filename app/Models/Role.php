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
}
