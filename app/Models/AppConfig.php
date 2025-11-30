<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppConfig extends Model
{
    protected $table = 'app_config';

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * Get a config value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("app_config.{$key}", 3600, function () use ($key, $default) {
            $config = static::where('key', $key)->first();
            return $config ? $config->value : $default;
        });
    }

    /**
     * Set a config value.
     */
    public static function setValue(string $key, mixed $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );
        
        Cache::forget("app_config.{$key}");
    }

    /**
     * Get full app configuration for API response.
     */
    public static function getFullConfig(): array
    {
        return [
            'app' => static::getValue('app', [
                'name' => 'Kiasi Daily',
                'version' => '1.0.0',
                'minimum_version' => '1.0.0',
                'force_update' => false,
                'maintenance_mode' => false,
                'maintenance_message' => null,
            ]),
            'languages' => static::getValue('languages', [
                [
                    'code' => 'sw',
                    'name' => 'Kiswahili',
                    'native_name' => 'Kiswahili',
                    'is_default' => true,
                ],
                [
                    'code' => 'en',
                    'name' => 'English',
                    'native_name' => 'English',
                    'is_default' => false,
                ],
            ]),
            'currencies' => static::getValue('currencies', [
                [
                    'code' => 'TZS',
                    'name' => 'Tanzanian Shilling',
                    'symbol' => 'TSh',
                    'decimal_places' => 0,
                    'is_default' => true,
                ],
                [
                    'code' => 'USD',
                    'name' => 'US Dollar',
                    'symbol' => '$',
                    'decimal_places' => 2,
                    'is_default' => false,
                ],
                [
                    'code' => 'KES',
                    'name' => 'Kenyan Shilling',
                    'symbol' => 'KSh',
                    'decimal_places' => 2,
                    'is_default' => false,
                ],
                [
                    'code' => 'UGX',
                    'name' => 'Ugandan Shilling',
                    'symbol' => 'USh',
                    'decimal_places' => 0,
                    'is_default' => false,
                ],
            ]),
            'date_formats' => static::getValue('date_formats', [
                [
                    'format' => 'DD/MM/YYYY',
                    'example' => '25/12/2024',
                    'is_default' => true,
                ],
                [
                    'format' => 'MM/DD/YYYY',
                    'example' => '12/25/2024',
                    'is_default' => false,
                ],
                [
                    'format' => 'YYYY-MM-DD',
                    'example' => '2024-12-25',
                    'is_default' => false,
                ],
            ]),
            'support' => static::getValue('support', [
                'email' => 'support@kiasidaily.com',
                'phone' => '+255 123 456 789',
                'whatsapp' => '+255 123 456 789',
            ]),
            'social' => static::getValue('social', [
                'facebook' => 'https://facebook.com/kiasidaily',
                'twitter' => 'https://twitter.com/kiasidaily',
                'instagram' => 'https://instagram.com/kiasidaily',
            ]),
        ];
    }
}

