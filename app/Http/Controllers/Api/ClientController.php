<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Client::ofCompany($user->company_id)->with(['assignedTo:id,name']);

        if (!$user->isAdmin() && !$user->isManager()) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->has('type')) {
            $request->type === 'buyer' ? $query->buyers() : $query->sellers();
        }
        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy($request->get('sort_by', 'created_at'), $request->get('sort_order', 'desc'))
            ->paginate($request->get('per_page', 20));

        return response()->json(['success' => true, 'data' => $clients->items(), 'meta' => [
            'current_page' => $clients->currentPage(),
            'last_page' => $clients->lastPage(),
            'total' => $clients->total(),
        ]]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'type' => 'required|in:buyer,seller,both',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $client = Client::create(array_merge($request->all(), [
            'company_id' => $request->user()->company_id,
            'assigned_to' => $request->assigned_to ?? $request->user()->id,
            'status' => 'active',
        ]));

        ActivityLog::log('created', "Added client: {$client->name}", $client);
        return response()->json(['success' => true, 'data' => $client], 201);
    }

    public function show(Request $request, $id)
    {
        $client = Client::ofCompany($request->user()->company_id)
            ->with(['assignedTo:id,name', 'lead', 'followUps', 'dealsAsBuyer', 'dealsAsSeller'])
            ->find($id);

        if (!$client) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        return response()->json(['success' => true, 'data' => $client]);
    }

    public function update(Request $request, $id)
    {
        $client = Client::ofCompany($request->user()->company_id)->find($id);
        if (!$client) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $client->update($request->all());
        ActivityLog::log('updated', "Updated client: {$client->name}", $client);
        return response()->json(['success' => true, 'data' => $client]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $client = Client::ofCompany($user->company_id)->find($id);
        if (!$client) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        if (!$user->isAdmin()) return response()->json(['success' => false, 'message' => 'Forbidden'], 403);

        $client->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
