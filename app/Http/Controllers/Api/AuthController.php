<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new company and admin user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'city' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create company
        $company = Company::create([
            'name' => $request->company_name,
            'slug' => Str::slug($request->company_name) . '-' . Str::random(5),
            'city' => $request->city,
        ]);

        // Create default lead statuses and property types
        $company->createDefaultLeadStatuses();
        $company->createDefaultPropertyTypes();

        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();

        // Create admin user
        $user = User::create([
            'company_id' => $company->id,
            'role_id' => $adminRole->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Create API token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $adminRole->display_name,
                ],
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact your administrator.',
            ], 403);
        }

        // Revoke previous tokens (optional - for single device login)
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'role' => $user->role?->display_name,
                    'role_name' => $user->role?->name,
                    'permissions' => $user->role?->permissions,
                ],
                'company' => [
                    'id' => $user->company?->id,
                    'name' => $user->company?->name,
                    'logo' => $user->company?->logo,
                    'onboarding_completed' => $user->company?->onboarding_completed_at !== null,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Get current user profile.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load(['role', 'company']);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'role' => $user->role?->display_name,
                    'role_name' => $user->role?->name,
                    'permissions' => $user->role?->permissions,
                    'preferences' => $user->preferences,
                ],
                'company' => [
                    'id' => $user->company?->id,
                    'name' => $user->company?->name,
                    'logo' => $user->company?->logo,
                    'city' => $user->company?->city,
                    'settings' => $user->company?->settings,
                    'onboarding_completed' => $user->company?->onboarding_completed_at !== null,
                ],
            ],
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'avatar' => 'sometimes|nullable|string',
            'preferences' => 'sometimes|nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update($request->only(['name', 'phone', 'avatar', 'preferences']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'preferences' => $user->preferences,
                ],
            ],
        ]);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Complete company onboarding.
     */
    public function completeOnboarding(Request $request)
    {
        $user = $request->user();
        $company = $user->company;

        $validator = Validator::make($request->all(), [
            'city' => 'sometimes|string|max:255',
            'operating_areas' => 'sometimes|array',
            'property_types' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $company->update([
            'city' => $request->city ?? $company->city,
            'operating_areas' => $request->operating_areas ?? $company->operating_areas,
            'onboarding_completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding completed successfully',
            'data' => [
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'city' => $company->city,
                    'operating_areas' => $company->operating_areas,
                    'onboarding_completed' => true,
                ],
            ],
        ]);
    }
}
