<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PageContent;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Get published properties for frontend.
     */
    public function getProperties(Request $request)
    {
        $query = Property::where('is_published', true)
            ->where('is_active', true)
            ->with(['propertyType', 'images']);

        // Filter by category (residential/commercial)
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by listing type (sale/rent)
        if ($request->has('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filter by property type
        if ($request->has('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by BHK
        if ($request->has('bhk')) {
            $query->where('bhk', $request->bhk);
        }

        // Featured first
        $query->orderBy('is_featured', 'desc')
              ->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 12);
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $properties
        ]);
    }

    /**
     * Get a single published property.
     */
    public function getProperty($id)
    {
        $property = Property::where('is_published', true)
            ->where('is_active', true)
            ->with(['propertyType', 'images'])
            ->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $property
        ]);
    }

    /**
     * Get page content for frontend (no auth required).
     */
    public function getPageContent($pageKey)
    {
        // For now, get from first company (can be enhanced for multi-tenant)
        $contents = PageContent::where('page_key', $pageKey)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->keyBy('section_key');

        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }

    /**
     * Get featured properties.
     */
    public function getFeaturedProperties()
    {
        $properties = Property::where('is_published', true)
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['propertyType', 'images'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties
        ]);
    }

    /**
     * Get property statistics for frontend.
     */
    public function getStats()
    {
        $stats = [
            'total_properties' => Property::where('is_published', true)->count(),
            'residential' => Property::where('is_published', true)->where('category', 'residential')->count(),
            'commercial' => Property::where('is_published', true)->where('category', 'commercial')->count(),
            'for_sale' => Property::where('is_published', true)->where('listing_type', 'sale')->count(),
            'for_rent' => Property::where('is_published', true)->where('listing_type', 'rent')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
