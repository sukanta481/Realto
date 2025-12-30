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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Assigned to
            
            // Polymorphic relation - can be for lead or client
            $table->string('followable_type'); // Lead or Client
            $table->unsignedBigInteger('followable_id');
            
            // Task details
            $table->string('purpose');
            $table->text('notes')->nullable();
            $table->dateTime('scheduled_at');
            $table->dateTime('completed_at')->nullable();
            
            // Priority & Status
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['pending', 'completed', 'cancelled', 'rescheduled'])->default('pending');
            
            // Outcome (filled when completed)
            $table->text('outcome')->nullable();
            $table->dateTime('next_follow_up')->nullable();
            
            // Reminders
            $table->boolean('reminder_sent')->default(false);
            $table->dateTime('remind_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'user_id', 'scheduled_at']);
            $table->index(['company_id', 'status', 'scheduled_at']);
            $table->index(['followable_type', 'followable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
