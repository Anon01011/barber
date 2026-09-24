<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            // Customer Data Privacy Settings
            ['key' => 'customer_data_masking_enabled', 'value' => json_encode(false), 'type' => 'security'],
            ['key' => 'mask_customer_phone', 'value' => json_encode(false), 'type' => 'security'],
            ['key' => 'mask_customer_email', 'value' => json_encode(false), 'type' => 'security'],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();

            DB::table('settings')->updateOrInsert(
                [
                    'key' => $setting['key'],
                    'salon_id' => null,
                    'branch_id' => null,
                ],
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
            'customer_data_masking_enabled',
            'mask_customer_phone',
            'mask_customer_email',
        ];

        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
