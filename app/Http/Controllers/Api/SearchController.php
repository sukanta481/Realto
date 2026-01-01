<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Client;
use App\Models\Deal;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across all entities.
     */
    public function search(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;
        $query = $request->get('q', '');
        $type = $request->get('type'); // Optional: filter by type
        $limit = min($request->get('limit', 10), 50);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Query too short',
            ]);
        }

        $results = [];
        $searchTerm = "%{$query}%";

        // Search Leads
        if (!$type || $type === 'leads') {
            $leads = Lead::ofCompany($companyId)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('phone', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm)
                      ->orWhere('location_preference', 'like', $searchTerm);
                })
                ->with('status:id,name,color')
                ->limit($limit)
                ->get()
                ->map(function ($lead) {
                    return [
                        'id' => $lead->id,
                        'type' => 'lead',
                        'title' => $lead->name,
                        'subtitle' => $lead->phone,
                        'description' => $lead->location_preference,
                        'status' => $lead->status?->name,
                        'status_color' => $lead->status?->color,
                        'url' => "/leads/{$lead->id}",
                        'icon' => 'user',
                    ];
                });

            $results = array_merge($results, $leads->toArray());
        }

        // Search Properties
        if (!$type || $type === 'properties') {
            $properties = Property::ofCompany($companyId)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                      ->orWhere('address', 'like', $searchTerm)
                      ->orWhere('locality', 'like', $searchTerm)
                      ->orWhere('city', 'like', $searchTerm)
                      ->orWhere('owner_name', 'like', $searchTerm);
                })
                ->limit($limit)
                ->get()
                ->map(function ($property) {
                    return [
                        'id' => $property->id,
                        'type' => 'property',
                        'title' => $property->title,
                        'subtitle' => $property->formatted_price,
                        'description' => $property->full_address,
                        'status' => ucfirst($property->status),
                        'status_color' => $property->status === 'available' ? '#22c55e' : '#6366f1',
                        'url' => "/properties/{$property->id}",
                        'icon' => 'building',
                    ];
                });

            $results = array_merge($results, $properties->toArray());
        }

        // Search Clients
        if (!$type || $type === 'clients') {
            $clients = Client::ofCompany($companyId)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('phone', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm)
                      ->orWhere('city', 'like', $searchTerm);
                })
                ->limit($limit)
                ->get()
                ->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'type' => 'client',
                        'title' => $client->name,
                        'subtitle' => $client->phone,
                        'description' => $client->city,
                        'status' => ucfirst($client->type ?? 'Client'),
                        'status_color' => '#8b5cf6',
                        'url' => "/clients/{$client->id}",
                        'icon' => 'users',
                    ];
                });

            $results = array_merge($results, $clients->toArray());
        }

        // Search Deals
        if (!$type || $type === 'deals') {
            $deals = Deal::ofCompany($companyId)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                      ->orWhere('notes', 'like', $searchTerm);
                })
                ->with(['property:id,title', 'buyer:id,name'])
                ->limit($limit)
                ->get()
                ->map(function ($deal) {
                    return [
                        'id' => $deal->id,
                        'type' => 'deal',
                        'title' => $deal->title ?: ($deal->property?->title ?? 'Deal #' . $deal->id),
                        'subtitle' => $deal->formatted_value,
                        'description' => $deal->buyer?->name,
                        'status' => $deal->stage_name,
                        'status_color' => $deal->stage_color,
                        'url' => "/deals/{$deal->id}",
                        'icon' => 'briefcase',
                    ];
                });

            $results = array_merge($results, $deals->toArray());
        }

        // Sort by relevance (exact matches first)
        usort($results, function ($a, $b) use ($query) {
            $aExact = stripos($a['title'], $query) === 0 ? 0 : 1;
            $bExact = stripos($b['title'], $query) === 0 ? 0 : 1;
            return $aExact - $bExact;
        });

        // Limit total results
        $results = array_slice($results, 0, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
            'meta' => [
                'query' => $query,
                'count' => count($results),
            ],
        ]);
    }

    /**
     * Get recent searches for the user.
     */
    public function recent(Request $request)
    {
        // Could store in cache or database
        // For now, return empty
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}
