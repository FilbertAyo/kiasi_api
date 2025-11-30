<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Register a device token for push notifications.
     * POST /api/v1/device/register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:500'],
            'platform' => ['required', 'string', 'in:android,ios'],
            'device_name' => ['nullable', 'string', 'max:100'],
            'device_model' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $request->user();

        // Check if token already exists for another user (transfer ownership)
        $existingToken = DeviceToken::where('token', $validated['token'])->first();

        if ($existingToken) {
            if ($existingToken->user_id !== $user->id) {
                // Token exists for another user - transfer it
                $existingToken->update([
                    'user_id' => $user->id,
                    'platform' => $validated['platform'],
                    'device_name' => $validated['device_name'] ?? null,
                    'device_model' => $validated['device_model'] ?? null,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            } else {
                // Token exists for same user - just update it
                $existingToken->update([
                    'platform' => $validated['platform'],
                    'device_name' => $validated['device_name'] ?? $existingToken->device_name,
                    'device_model' => $validated['device_model'] ?? $existingToken->device_model,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'code' => 'DEVICE_REGISTERED',
                'message' => 'Device registered successfully',
                'data' => [
                    'token_id' => $existingToken->id,
                    'platform' => $existingToken->platform,
                ],
            ]);
        }

        // Create new token
        $deviceToken = DeviceToken::create([
            'user_id' => $user->id,
            'token' => $validated['token'],
            'platform' => $validated['platform'],
            'device_name' => $validated['device_name'] ?? null,
            'device_model' => $validated['device_model'] ?? null,
            'is_active' => true,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 'DEVICE_REGISTERED',
            'message' => 'Device registered successfully',
            'data' => [
                'token_id' => $deviceToken->id,
                'platform' => $deviceToken->platform,
            ],
        ], 201);
    }

    /**
     * Unregister a device token (e.g., on logout).
     * DELETE /api/v1/device/unregister
     */
    public function unregister(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $user = $request->user();

        $deleted = DeviceToken::where('user_id', $user->id)
            ->where('token', $validated['token'])
            ->delete();

        if ($deleted) {
            return response()->json([
                'status' => 'success',
                'code' => 'DEVICE_UNREGISTERED',
                'message' => 'Device unregistered successfully',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'code' => 'DEVICE_NOT_FOUND',
            'message' => 'Device token not found',
        ], 404);
    }

    /**
     * Get user's registered devices.
     * GET /api/v1/device/list
     */
    public function list(Request $request): JsonResponse
    {
        $user = $request->user();

        $devices = $user->deviceTokens()
            ->select(['id', 'platform', 'device_name', 'device_model', 'is_active', 'last_used_at', 'created_at'])
            ->orderByDesc('last_used_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'code' => 'DEVICES_FETCHED',
            'message' => 'Devices retrieved successfully',
            'data' => $devices,
        ]);
    }
}

