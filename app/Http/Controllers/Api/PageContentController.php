<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PageContentController extends Controller
{
    /**
     * Get all page contents.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        
        $contents = PageContent::where('company_id', $companyId)
            ->orderBy('page_key')
            ->orderBy('order')
            ->get()
            ->groupBy('page_key');
            
        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }

    /**
     * Get contents for a specific page.
     */
    public function getPage(Request $request, $pageKey)
    {
        $companyId = $request->user()->company_id;
        
        $contents = PageContent::where('company_id', $companyId)
            ->forPage($pageKey)
            ->get()
            ->keyBy('section_key');
            
        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }

    /**
     * Update or create a content section.
     */
    public function update(Request $request, $pageKey, $sectionKey)
    {
        $companyId = $request->user()->company_id;
        
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'content' => 'nullable|array',
            'image_url' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $content = PageContent::updateOrCreate(
            [
                'company_id' => $companyId,
                'page_key' => $pageKey,
                'section_key' => $sectionKey
            ],
            $request->only([
                'title', 'subtitle', 'description', 'content',
                'image_url', 'button_text', 'button_url', 'order', 'is_active'
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully',
            'data' => $content
        ]);
    }

    /**
     * Bulk update page contents.
     */
    public function bulkUpdate(Request $request, $pageKey)
    {
        $companyId = $request->user()->company_id;
        $sections = $request->input('sections', []);
        
        foreach ($sections as $sectionKey => $data) {
            PageContent::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'page_key' => $pageKey,
                    'section_key' => $sectionKey
                ],
                array_merge($data, ['company_id' => $companyId])
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Page content updated successfully'
        ]);
    }

    /**
     * Upload an image for content.
     */
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('image');
        $path = $file->store('content', 'public');
        
        return response()->json([
            'success' => true,
            'url' => '/storage/' . $path
        ]);
    }

    /**
     * Delete a content section.
     */
    public function destroy(Request $request, $id)
    {
        $companyId = $request->user()->company_id;
        
        $content = PageContent::where('company_id', $companyId)
            ->where('id', $id)
            ->first();

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found'
            ], 404);
        }

        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully'
        ]);
    }

    /**
     * Get available pages for content management.
     */
    public function getPages()
    {
        $pages = [
            ['key' => 'home', 'name' => 'Home Page', 'sections' => ['hero', 'stats', 'why_us', 'services', 'cta']],
            ['key' => 'about', 'name' => 'About Page', 'sections' => ['hero', 'story', 'mission', 'vision', 'team']],
            ['key' => 'services', 'name' => 'Services Page', 'sections' => ['hero', 'services', 'process', 'faq']],
            ['key' => 'contact', 'name' => 'Contact Page', 'sections' => ['hero', 'info', 'offices', 'hours']],
            ['key' => 'residential', 'name' => 'Residential Page', 'sections' => ['hero', 'filters']],
            ['key' => 'commercial', 'name' => 'Commercial Page', 'sections' => ['hero', 'categories']],
        ];

        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    }
}
