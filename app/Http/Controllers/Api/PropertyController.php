<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Get all properties with filters.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $query = Property::ofCompany($companyId)
            ->with(['propertyType:id,name', 'addedBy:id,name']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('locality')) {
            $query->where('locality', 'like', "%{$request->locality}%");
        }

        if ($request->filled('bhk')) {
            $query->where('bhk', $request->bhk);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('locality', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Only show active by default
        if (!$request->has('show_inactive')) {
            $query->where('is_active', true);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $properties->items(),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }

    /**
     * Create a new property.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'listing_type' => 'required|in:sale,rent',
            'property_type_id' => 'nullable|exists:property_types,id',
            'address' => 'nullable|string|max:500',
            'locality' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'bhk' => 'nullable|string|max:20',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'balconies' => 'nullable|integer|min:0',
            'carpet_area' => 'nullable|numeric|min:0',
            'built_up_area' => 'nullable|numeric|min:0',
            'super_built_up_area' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|in:sqft,sqm,katha,acre',
            'floor' => 'nullable|integer',
            'total_floors' => 'nullable|integer',
            'facing' => 'nullable|string|max:50',
            'age_of_property' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'price_per_sqft' => 'nullable|numeric|min:0',
            'price_negotiable' => 'nullable|boolean',
            'maintenance' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'furnishing' => 'nullable|in:unfurnished,semi-furnished,fully-furnished',
            'furnishing_details' => 'nullable|array',
            'status' => 'nullable|in:available,hold,sold,rented',
            'availability' => 'nullable|in:ready,under_construction',
            'possession_date' => 'nullable|date',
            'images' => 'nullable|array',
            'video_url' => 'nullable|url',
            'owner_name' => 'nullable|string|max:255',
            'owner_phone' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:100',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $property = Property::create(array_merge(
            $request->all(),
            [
                'company_id' => $user->company_id,
                'added_by' => $user->id,
                'is_active' => true,
            ]
        ));

        ActivityLog::log('created', "Added new property: {$property->title}", $property);

        $property->load(['propertyType:id,name', 'addedBy:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Property created successfully',
            'data' => $property,
        ], 201);
    }

    /**
     * Get a single property.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)
            ->with([
                'propertyType:id,name',
                'addedBy:id,name,phone,email',
                'visits' => function ($q) {
                    $q->with(['lead:id,name', 'client:id,name'])
                      ->orderBy('visit_date', 'desc')
                      ->limit(10);
                },
                'deals' => function ($q) {
                    $q->with(['buyer:id,name', 'handledBy:id,name'])
                      ->orderBy('created_at', 'desc');
                },
            ])
            ->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        // Get matching leads
        $matchingLeads = $property->getMatchingLeads()->take(10);

        return response()->json([
            'success' => true,
            'data' => [
                'property' => $property,
                'formatted_price' => $property->formatted_price,
                'display_area' => $property->display_area,
                'full_address' => $property->full_address,
                'primary_image' => $property->primary_image,
                'matching_leads' => $matchingLeads,
            ],
        ]);
    }

    /**
     * Update a property.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'listing_type' => 'sometimes|in:sale,rent',
            'property_type_id' => 'nullable|exists:property_types,id',
            'address' => 'nullable|string|max:500',
            'locality' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'bhk' => 'nullable|string|max:20',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'balconies' => 'nullable|integer|min:0',
            'carpet_area' => 'nullable|numeric|min:0',
            'built_up_area' => 'nullable|numeric|min:0',
            'super_built_up_area' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|in:sqft,sqm,katha,acre',
            'floor' => 'nullable|integer',
            'total_floors' => 'nullable|integer',
            'facing' => 'nullable|string|max:50',
            'age_of_property' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'price_per_sqft' => 'nullable|numeric|min:0',
            'price_negotiable' => 'nullable|boolean',
            'maintenance' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'furnishing' => 'nullable|in:unfurnished,semi-furnished,fully-furnished',
            'furnishing_details' => 'nullable|array',
            'status' => 'nullable|in:available,hold,sold,rented',
            'availability' => 'nullable|in:ready,under_construction',
            'possession_date' => 'nullable|date',
            'images' => 'nullable|array',
            'video_url' => 'nullable|url',
            'owner_name' => 'nullable|string|max:255',
            'owner_phone' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:100',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $property->update($request->all());

        ActivityLog::log('updated', "Updated property: {$property->title}", $property);

        $property->load(['propertyType:id,name', 'addedBy:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Property updated successfully',
            'data' => $property,
        ]);
    }

    /**
     * Update property status.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,hold,sold,rented',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $property->status;
        $property->update(['status' => $request->status]);

        ActivityLog::log('status_changed', "Changed property status from {$oldStatus} to {$request->status}", $property);

        return response()->json([
            'success' => true,
            'message' => 'Property status updated',
            'data' => $property,
        ]);
    }

    /**
     * Delete a property.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        if (!$user->isAdmin() && !$user->isManager()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete properties',
            ], 403);
        }

        $propertyTitle = $property->title;
        $property->delete();

        ActivityLog::log('deleted', "Deleted property: {$propertyTitle}");

        return response()->json([
            'success' => true,
            'message' => 'Property deleted successfully',
        ]);
    }

    /**
     * Get property statistics.
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $stats = [
            'total' => Property::ofCompany($companyId)->count(),
            'available' => Property::ofCompany($companyId)->where('status', 'available')->count(),
            'sold' => Property::ofCompany($companyId)->where('status', 'sold')->count(),
            'rented' => Property::ofCompany($companyId)->where('status', 'rented')->count(),
            'hold' => Property::ofCompany($companyId)->where('status', 'hold')->count(),
            'for_sale' => Property::ofCompany($companyId)->where('listing_type', 'sale')->where('status', 'available')->count(),
            'for_rent' => Property::ofCompany($companyId)->where('listing_type', 'rent')->where('status', 'available')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get property types for dropdown.
     */
    public function types(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        
        // Check if PropertyType model exists and has data
        $types = [];
        if (class_exists('\App\Models\PropertyType')) {
            $types = \App\Models\PropertyType::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name']);
        }
        
        // Return default types if none found
        if (empty($types) || $types->isEmpty()) {
            $types = [
                ['id' => 'apartment', 'name' => 'Apartment'],
                ['id' => 'villa', 'name' => 'Villa'],
                ['id' => 'house', 'name' => 'Independent House'],
                ['id' => 'plot', 'name' => 'Plot / Land'],
                ['id' => 'commercial', 'name' => 'Commercial Space'],
                ['id' => 'office', 'name' => 'Office Space'],
                ['id' => 'shop', 'name' => 'Shop / Showroom'],
                ['id' => 'penthouse', 'name' => 'Penthouse'],
                ['id' => 'studio', 'name' => 'Studio Apartment'],
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    /**
     * Upload property images.
     */
    public function uploadImages(Request $request, $id)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'image|max:5120', // 5MB max per image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $uploadedImages = [];
        $hasExistingImages = $property->propertyImages()->exists();
        $order = $property->propertyImages()->max('order') ?? 0;

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('properties/' . $property->id, 'public');
            
            $propertyImage = $property->propertyImages()->create([
                'filename' => $image->getClientOriginalName(),
                'path' => $path,
                'disk' => 'public',
                'mime_type' => $image->getMimeType(),
                'size' => $image->getSize(),
                'is_primary' => !$hasExistingImages && $index === 0,
                'order' => ++$order,
            ]);

            $uploadedImages[] = $propertyImage;
        }

        $count = count($uploadedImages);
        ActivityLog::log('updated', "Added {$count} image(s) to property: {$property->title}", $property);

        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'data' => $uploadedImages,
        ]);
    }

    /**
     * Delete a property image.
     */
    public function deleteImage(Request $request, $id, $imageId)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $image = $property->propertyImages()->find($imageId);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
            ], 404);
        }

        $wasPrimary = $image->is_primary;
        $image->delete(); // This will also delete the file via model boot method

        // If deleted image was primary, set first remaining image as primary
        if ($wasPrimary) {
            $firstImage = $property->propertyImages()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
        ]);
    }

    /**
     * Set an image as primary.
     */
    public function setPrimaryImage(Request $request, $id, $imageId)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $image = $property->propertyImages()->find($imageId);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
            ], 404);
        }

        // Remove primary from all images
        $property->propertyImages()->update(['is_primary' => false]);
        
        // Set this image as primary
        $image->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image updated',
            'data' => $image,
        ]);
    }

    /**
     * Toggle property publish status.
     */
    public function togglePublish(Request $request, $id)
    {
        $user = $request->user();

        $property = Property::ofCompany($user->company_id)->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        $property->is_published = !$property->is_published;
        $property->save();

        $status = $property->is_published ? 'published' : 'unpublished';
        ActivityLog::log('updated', "Property {$status}: {$property->title}", $property);

        return response()->json([
            'success' => true,
            'message' => "Property {$status} successfully",
            'data' => $property,
        ]);
    }
}

