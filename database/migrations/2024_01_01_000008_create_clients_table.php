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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Personal Info
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            
            // Additional Info
            $table->string('occupation')->nullable();
            $table->date('dob')->nullable();
            $table->date('anniversary')->nullable();
            
            // Classification
            $table->enum('type', ['buyer', 'seller', 'both'])->default('buyer');
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'status']);
            $table->index('phone');
        });

        // Add foreign key to leads table for converted client
        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('converted_client_id')->references('id')->on('clients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['converted_client_id']);
        });
        Schema::dropIfExists('clients');
    }
};
