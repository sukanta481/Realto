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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Parties involved
            $table->foreignId('buyer_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('seller_id')->nullable()->constrained('clients')->onDelete('set null');
            
            // Deal Details
            $table->string('title');
            $table->enum('type', ['sale', 'rent'])->default('sale');
            $table->decimal('deal_value', 15, 2);
            
            // Commission
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->decimal('commission_amount', 15, 2)->nullable();
            $table->enum('commission_from', ['buyer', 'seller', 'both'])->default('seller');
            
            // Stages
            $table->enum('stage', ['open', 'negotiation', 'agreement', 'documentation', 'closed_won', 'closed_lost'])->default('open');
            
            // Dates
            $table->date('expected_close_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->date('agreement_date')->nullable();
            $table->date('registration_date')->nullable();
            
            // Payment tracking
            $table->enum('payment_status', ['pending', 'partial', 'received'])->default('pending');
            $table->decimal('amount_received', 15, 2)->default(0);
            
            // Notes
            $table->text('notes')->nullable();
            
            // Close reason (if lost)
            $table->string('close_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'stage']);
            $table->index(['company_id', 'handled_by']);
            $table->index(['company_id', 'closed_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
