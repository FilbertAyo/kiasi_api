<?php

namespace Database\Seeders;

use App\Models\AppConfig;
use Illuminate\Database\Seeder;

class AppConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // App info
        AppConfig::setValue('app', [
            'name' => 'Kiasi Daily',
            'version' => '1.0.0',
            'minimum_version' => '1.0.0',
            'force_update' => false,
            'maintenance_mode' => false,
            'maintenance_message' => null,
        ], 'App information and version settings');

        // Supported languages
        AppConfig::setValue('languages', [
            [
                'code' => 'sw',
                'name' => 'Kiswahili',
                'native_name' => 'Kiswahili',
                'is_default' => false,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'is_default' => true,
            ],
        ], 'Supported languages');

        // Supported currencies
        AppConfig::setValue('currencies', [
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
        ], 'Supported currencies');

        // Date formats
        AppConfig::setValue('date_formats', [
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
        ], 'Available date formats');

        // Support contact info
        AppConfig::setValue('support', [
            'email' => 'support@kiasidaily.com',
            'phone' => '+255 755 237 692',
            'whatsapp' => '+255 755 237 692',
        ], 'Support contact information');

        // Social media links
        AppConfig::setValue('social', [
            'facebook' => 'https://facebook.com/kiasidaily',
            'twitter' => 'https://twitter.com/kiasidaily',
            'instagram' => 'https://instagram.com/kiasidaily',
        ], 'Social media links');

        // Company info
        AppConfig::setValue('company', [
            'name' => 'Kiasi Daily',
            'address' => 'Dar es Salaam, Tanzania',
            'email' => 'info@kiasidaily.com',
            'website' => 'https://kiasidaily.com',
        ], 'Company information');

        // App store URLs
        AppConfig::setValue('update_urls', [
            'android' => 'https://play.google.com/store/apps/details?id=com.kiasidaily',
            'ios' => 'https://apps.apple.com/app/kiasi-daily/id123456789',
        ], 'App store URLs for updates');

        // Open source credits
        AppConfig::setValue('credits', [
            [
                'name' => 'Flutter',
                'url' => 'https://flutter.dev',
                'license' => 'BSD-3-Clause',
            ],
            [
                'name' => 'Laravel',
                'url' => 'https://laravel.com',
                'license' => 'MIT',
            ],
        ], 'Open source credits');
    }
}

