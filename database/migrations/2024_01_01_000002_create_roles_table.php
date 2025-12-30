<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // admin, manager, agent, telecaller
            $table->string('display_name');
            $table->json('permissions')->nullable(); // Granular permissions
            $table->boolean('is_system')->default(false); // System roles can't be deleted
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Admin', 'permissions' => json_encode(['*']), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manager', 'display_name' => 'Manager', 'permissions' => json_encode(['leads.*', 'properties.*', 'clients.*', 'deals.*', 'follow_ups.*', 'reports.view', 'team.view']), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'agent', 'display_name' => 'Agent', 'permissions' => json_encode(['leads.own', 'properties.view', 'clients.own', 'deals.own', 'follow_ups.own']), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'telecaller', 'display_name' => 'Telecaller', 'permissions' => json_encode(['leads.own', 'follow_ups.own']), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
