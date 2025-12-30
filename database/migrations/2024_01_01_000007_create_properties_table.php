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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('property_type_id')->nullable()->constrained()->onDelete('set null');
            
            // Basic Info
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('listing_type', ['sale', 'rent'])->default('sale');
            
            // Location
            $table->string('address')->nullable();
            $table->string('locality')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Property Details
            $table->string('bhk')->nullable(); // 1BHK, 2BHK, etc.
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('balconies')->nullable();
            $table->decimal('carpet_area', 10, 2)->nullable();
            $table->decimal('built_up_area', 10, 2)->nullable();
            $table->decimal('super_built_up_area', 10, 2)->nullable();
            $table->string('area_unit')->default('sqft'); // sqft, sqm, katha, acre
            $table->integer('floor')->nullable();
            $table->integer('total_floors')->nullable();
            $table->string('facing')->nullable(); // North, South, East, West
            $table->integer('age_of_property')->nullable(); // in years
            
            // Pricing
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('price_per_sqft', 10, 2)->nullable();
            $table->boolean('price_negotiable')->default(true);
            $table->decimal('maintenance', 10, 2)->nullable();
            $table->decimal('security_deposit', 15, 2)->nullable(); // For rent
            
            // Features
            $table->json('amenities')->nullable(); // parking, gym, pool, etc.
            $table->json('furnishing_details')->nullable();
            $table->enum('furnishing', ['unfurnished', 'semi-furnished', 'fully-furnished'])->nullable();
            
            // Status
            $table->enum('status', ['available', 'hold', 'sold', 'rented'])->default('available');
            $table->enum('availability', ['ready', 'under_construction'])->default('ready');
            $table->date('possession_date')->nullable();
            
            // Media
            $table->json('images')->nullable();
            $table->string('video_url')->nullable();
            
            // Owner/Source
            $table->string('owner_name')->nullable();
            $table->string('owner_phone')->nullable();
            $table->string('source')->nullable();
            
            // Visibility
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'listing_type']);
            $table->index(['company_id', 'city']);
            $table->index(['company_id', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
