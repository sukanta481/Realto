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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // What happened
            $table->string('action'); // created, updated, deleted, status_changed, etc.
            $table->string('description');
            
            // Subject of the action (polymorphic)
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            
            // Additional data
            $table->json('properties')->nullable(); // old values, new values, etc.
            
            // Request info for audit
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
