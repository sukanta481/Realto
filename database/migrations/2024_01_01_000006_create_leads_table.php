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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('lead_status_id')->nullable()->constrained()->onDelete('set null');
            
            // Contact Info
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('alternate_phone')->nullable();
            
            // Requirements
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->string('location_preference')->nullable();
            $table->json('preferred_locations')->nullable(); // Array of locations
            $table->string('property_type')->nullable(); // Flat, Land, Commercial, etc.
            $table->string('purpose')->nullable(); // Buy, Rent, Investment
            $table->string('bhk')->nullable(); // 1BHK, 2BHK, etc.
            $table->decimal('area_min', 10, 2)->nullable(); // sqft
            $table->decimal('area_max', 10, 2)->nullable();
            
            // Source tracking
            $table->string('source')->default('manual'); // manual, website, referral, etc.
            $table->string('source_details')->nullable();
            
            // Status
            $table->integer('priority')->default(2); // 1=high, 2=medium, 3=low
            $table->text('notes')->nullable();
            
            // Conversion
            $table->foreignId('converted_client_id')->nullable();
            $table->timestamp('converted_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'lead_status_id']);
            $table->index(['company_id', 'assigned_to']);
            $table->index(['company_id', 'created_at']);
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
