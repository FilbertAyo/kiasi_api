<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Get app configuration.
     * GET /api/v1/config
     */
    public function index(): JsonResponse
    {
        // Check maintenance mode first
        $appConfig = AppConfig::getValue('app', []);
        
        if ($appConfig['maintenance_mode'] ?? false) {
            return response()->json([
                'status' => 'error',
                'code' => 'MAINTENANCE_MODE',
                'message' => $appConfig['maintenance_message'] ?? 'The app is currently under maintenance. Please try again later.',
                'data' => [
                    'maintenance_mode' => true,
                    'maintenance_message' => $appConfig['maintenance_message'] ?? 'The app is currently under maintenance. Please try again later.',
                ],
            ], 503);
        }

        return response()->json([
            'status' => 'success',
            'code' => 'CONFIG_FETCHED',
            'message' => 'Configuration retrieved successfully',
            'data' => AppConfig::getFullConfig(),
        ]);
    }

    /**
     * Check app version and maintenance status.
     * GET /api/v1/config/version?current_version=1.0.0&platform=android
     */
    public function checkVersion(Request $request): JsonResponse
    {
        $request->validate([
            'current_version' => ['required', 'string'],
            'platform' => ['required', 'string', 'in:android,ios'],
        ]);

        $currentVersion = $request->query('current_version');
        $platform = $request->query('platform');

        // Get app config
        $appConfig = AppConfig::getValue('app', [
            'name' => 'Kiasi Daily',
            'version' => '1.0.0',
            'minimum_version' => '1.0.0',
            'force_update' => false,
            'maintenance_mode' => false,
            'maintenance_message' => null,
        ]);

        // Check maintenance mode first
        if ($appConfig['maintenance_mode'] ?? false) {
            return response()->json([
                'status' => 'error',
                'code' => 'MAINTENANCE_MODE',
                'message' => $appConfig['maintenance_message'] ?? 'The app is currently under maintenance. Please try again later.',
                'data' => [
                    'maintenance_mode' => true,
                    'maintenance_message' => $appConfig['maintenance_message'] ?? 'The app is currently under maintenance.',
                ],
            ], 503);
        }

        $latestVersion = $appConfig['version'] ?? '1.0.0';
        $minimumVersion = $appConfig['minimum_version'] ?? '1.0.0';
        $forceUpdateEnabled = $appConfig['force_update'] ?? false;

        // Compare versions
        $updateAvailable = version_compare($currentVersion, $latestVersion, '<');
        $belowMinimum = version_compare($currentVersion, $minimumVersion, '<');
        
        // Force update if below minimum OR if force_update flag is set and update is available
        $forceUpdate = $belowMinimum || ($forceUpdateEnabled && $updateAvailable);

        // Get platform-specific update URL
        $updateUrls = AppConfig::getValue('update_urls', [
            'android' => 'https://play.google.com/store/apps/details?id=com.kiasidaily',
            'ios' => 'https://apps.apple.com/app/kiasi-daily/id123456789',
        ]);

        // Get release notes if available
        $releaseNotes = AppConfig::getValue('release_notes', null);

        // Determine response code
        if ($forceUpdate) {
            $code = 'FORCE_UPDATE';
            $message = 'Please update to continue using the app';
        } elseif ($updateAvailable) {
            $code = 'UPDATE_AVAILABLE';
            $message = 'A new version is available';
        } else {
            $code = 'VERSION_OK';
            $message = 'App is up to date';
        }

        $data = [
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'minimum_version' => $minimumVersion,
            'update_available' => $updateAvailable,
            'force_update' => $forceUpdate,
            'update_url' => $updateAvailable ? ($updateUrls[$platform] ?? null) : null,
            'release_notes' => $updateAvailable ? $releaseNotes : null,
        ];

        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Quick status check for app startup.
     * GET /api/v1/config/status?current_version=1.0.0&platform=android
     */
    public function status(Request $request): JsonResponse
    {
        $request->validate([
            'current_version' => ['required', 'string'],
            'platform' => ['required', 'string', 'in:android,ios'],
        ]);

        $currentVersion = $request->query('current_version');
        $platform = $request->query('platform');

        $appConfig = AppConfig::getValue('app', [
            'version' => '1.0.0',
            'minimum_version' => '1.0.0',
            'force_update' => false,
            'maintenance_mode' => false,
            'maintenance_message' => null,
        ]);

        $updateUrls = AppConfig::getValue('update_urls', []);

        $latestVersion = $appConfig['version'] ?? '1.0.0';
        $minimumVersion = $appConfig['minimum_version'] ?? '1.0.0';
        $updateAvailable = version_compare($currentVersion, $latestVersion, '<');
        $belowMinimum = version_compare($currentVersion, $minimumVersion, '<');
        $forceUpdate = $belowMinimum || (($appConfig['force_update'] ?? false) && $updateAvailable);

        return response()->json([
            'status' => 'success',
            'code' => 'STATUS_OK',
            'message' => 'Status retrieved',
            'data' => [
                'maintenance_mode' => $appConfig['maintenance_mode'] ?? false,
                'maintenance_message' => $appConfig['maintenance_message'],
                'update_available' => $updateAvailable,
                'force_update' => $forceUpdate,
                'latest_version' => $latestVersion,
                'minimum_version' => $minimumVersion,
                'update_url' => $updateUrls[$platform] ?? null,
            ],
        ]);
    }
}
