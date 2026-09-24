<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'theme_color',
                'value' => json_encode('pink'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'business_name',
                'value' => json_encode(config('app.name')),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'business_email',
                'value' => json_encode(config('mail.from.address')),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'business_phone',
                'value' => json_encode(''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'business_address',
                'value' => json_encode(''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_facebook',
                'value' => json_encode(''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_instagram',
                'value' => json_encode(''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_twitter',
                'value' => json_encode(''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_linkedin',
                'value' => json_encode(''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'theme_color',
            'business_name',
            'business_email',
            'business_phone',
            'business_address',
            'social_facebook',
            'social_instagram',
            'social_twitter',
            'social_linkedin',
        ];

        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
