<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create default roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'permissions' => ['*'],
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'permissions' => [
                    'leads.view', 'leads.create', 'leads.edit', 'leads.delete',
                    'properties.view', 'properties.create', 'properties.edit', 'properties.delete',
                    'deals.view', 'deals.create', 'deals.edit', 'deals.delete',
                    'follow-ups.view', 'follow-ups.create', 'follow-ups.edit', 'follow-ups.delete',
                    'team.view',
                    'reports.view',
                ],
            ],
            [
                'name' => 'agent',
                'display_name' => 'Sales Agent',
                'permissions' => [
                    'leads.view', 'leads.create', 'leads.edit',
                    'properties.view',
                    'deals.view', 'deals.create', 'deals.edit',
                    'follow-ups.view', 'follow-ups.create', 'follow-ups.edit',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
