<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display all app settings.
     */
    public function index()
    {
        $settings = AppConfig::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update a specific setting.
     */
    public function update(Request $request, string $key)
    {
        $config = AppConfig::where('key', $key)->first();

        if (!$config) {
            return redirect()->route('admin.settings.index')->with('error', "Setting '{$key}' not found.");
        }

        // Build the value based on the key type
        $value = $this->buildValueFromRequest($request, $key, $config->value);

        $config->update(['value' => $value]);

        // Clear cache
        Cache::forget("app_config.{$key}");

        return redirect()->route('admin.settings.index')->with('success', ucfirst(str_replace('_', ' ', $key)) . " settings updated successfully.");
    }

    /**
     * Build value array from request based on setting key.
     */
    private function buildValueFromRequest(Request $request, string $key, array $currentValue): array
    {
        switch ($key) {
            case 'app':
                return [
                    'name' => $request->input('name', $currentValue['name'] ?? 'Kiasi Daily'),
                    'version' => $request->input('version', $currentValue['version'] ?? '1.0.0'),
                    'minimum_version' => $request->input('minimum_version', $currentValue['minimum_version'] ?? '1.0.0'),
                    'force_update' => $request->has('force_update'),
                    'maintenance_mode' => $request->has('maintenance_mode'),
                    'maintenance_message' => $request->input('maintenance_message', $currentValue['maintenance_message']),
                ];

            case 'support':
                return [
                    'email' => $request->input('email', $currentValue['email'] ?? ''),
                    'phone' => $request->input('phone', $currentValue['phone'] ?? ''),
                    'whatsapp' => $request->input('whatsapp', $currentValue['whatsapp'] ?? ''),
                ];

            case 'company':
                return [
                    'name' => $request->input('name', $currentValue['name'] ?? ''),
                    'address' => $request->input('address', $currentValue['address'] ?? ''),
                    'email' => $request->input('email', $currentValue['email'] ?? ''),
                    'website' => $request->input('website', $currentValue['website'] ?? ''),
                ];

            case 'social':
                return [
                    'facebook' => $request->input('facebook', $currentValue['facebook'] ?? ''),
                    'twitter' => $request->input('twitter', $currentValue['twitter'] ?? ''),
                    'instagram' => $request->input('instagram', $currentValue['instagram'] ?? ''),
                ];

            case 'update_urls':
                return [
                    'android' => $request->input('android', $currentValue['android'] ?? ''),
                    'ios' => $request->input('ios', $currentValue['ios'] ?? ''),
                ];

            default:
                // For other settings, try to parse JSON or return as-is
                $value = $request->input('value');
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }
                }
                return is_array($value) ? $value : $currentValue;
        }
    }
}
