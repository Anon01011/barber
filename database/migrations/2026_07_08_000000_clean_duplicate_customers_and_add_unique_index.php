<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Normalize empty string phone numbers to NULL to avoid duplicate key conflicts on empty strings
        DB::table('customers')
            ->where('phone', '')
            ->update(['phone' => null]);

        // 2. Identify duplicate customers within the same salon and having the same non-null phone number
        // We group by salon_id and phone across all records (active and soft-deleted)
        $duplicates = DB::table('customers')
            ->select('salon_id', 'phone', DB::raw('GROUP_CONCAT(id) as all_ids'))
            ->whereNotNull('phone')
            ->groupBy('salon_id', 'phone')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $allIds = explode(',', $dup->all_ids);

            // Order duplicate rows so that active records are preferred, then by minimum ID
            $rows = DB::table('customers')
                ->whereIn('id', $allIds)
                ->orderByRaw('deleted_at IS NULL DESC')
                ->orderBy('id', 'asc')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            $keepId = $rows->first()->id;
            $deleteIds = $rows->slice(1)->pluck('id')->toArray();

            if (empty($deleteIds)) {
                continue;
            }

            // Merge user_id if kept customer has NULL but a duplicate has a non-null user_id
            $keptCustomer = DB::table('customers')->where('id', $keepId)->first();
            if ($keptCustomer && is_null($keptCustomer->user_id)) {
                $userWithId = DB::table('customers')
                    ->whereIn('id', $deleteIds)
                    ->whereNotNull('user_id')
                    ->first();
                if ($userWithId) {
                    DB::table('customers')
                        ->where('id', $keepId)
                        ->update(['user_id' => $userWithId->user_id]);
                }
            }

            // Move bookings referencing duplicate IDs to the kept ID
            DB::table('bookings')
                ->whereIn('customer_id', $deleteIds)
                ->update(['customer_id' => $keepId]);

            // Move appointments referencing duplicate IDs to the kept ID
            if (Schema::hasTable('appointments')) {
                DB::table('appointments')
                    ->whereIn('customer_id', $deleteIds)
                    ->update(['customer_id' => $keepId]);
            }

            // Move pos_sales referencing duplicate IDs to the kept ID
            if (Schema::hasTable('pos_sales')) {
                DB::table('pos_sales')
                    ->whereIn('customer_id', $deleteIds)
                    ->update(['customer_id' => $keepId]);
            }

            // Move customer_memberships
            if (Schema::hasTable('customer_memberships')) {
                foreach ($deleteIds as $delId) {
                    $memberships = DB::table('customer_memberships')->where('customer_id', $delId)->get();
                    foreach ($memberships as $mship) {
                        $exists = DB::table('customer_memberships')
                            ->where('customer_id', $keepId)
                            ->where('membership_id', $mship->membership_id)
                            ->exists();
                        if ($exists) {
                            DB::table('customer_memberships')->where('id', $mship->id)->delete();
                        } else {
                            DB::table('customer_memberships')->where('id', $mship->id)->update(['customer_id' => $keepId]);
                        }
                    }
                }
            }

            // Move customer_package_balances
            if (Schema::hasTable('customer_package_balances')) {
                foreach ($deleteIds as $delId) {
                    $balances = DB::table('customer_package_balances')->where('customer_id', $delId)->get();
                    foreach ($balances as $bal) {
                        $existsQuery = DB::table('customer_package_balances')
                            ->where('customer_id', $keepId)
                            ->where('package_id', $bal->package_id)
                            ->where('service_id', $bal->service_id);
                        
                        if (isset($bal->pos_sale_id)) {
                            $existsQuery->where('pos_sale_id', $bal->pos_sale_id);
                        }
                        
                        if ($existsQuery->exists()) {
                            DB::table('customer_package_balances')->where('id', $bal->id)->delete();
                        } else {
                            DB::table('customer_package_balances')->where('id', $bal->id)->update(['customer_id' => $keepId]);
                        }
                    }
                }
            }

            // Move customer_favorite_services
            if (Schema::hasTable('customer_favorite_services')) {
                foreach ($deleteIds as $delId) {
                    $favs = DB::table('customer_favorite_services')->where('customer_id', $delId)->get();
                    foreach ($favs as $fav) {
                        $exists = DB::table('customer_favorite_services')
                            ->where('customer_id', $keepId)
                            ->where('service_id', $fav->service_id)
                            ->exists();
                        if ($exists) {
                            DB::table('customer_favorite_services')->where('id', $fav->id)->delete();
                        } else {
                            DB::table('customer_favorite_services')->where('id', $fav->id)->update(['customer_id' => $keepId]);
                        }
                    }
                }
            }

            // Move payments
            if (Schema::hasTable('payments')) {
                DB::table('payments')
                    ->whereIn('customer_id', $deleteIds)
                    ->update(['customer_id' => $keepId]);
            }

            // Move ratings
            if (Schema::hasTable('ratings')) {
                DB::table('ratings')
                    ->whereIn('customer_id', $deleteIds)
                    ->update(['customer_id' => $keepId]);
            }

            // Hard delete duplicate customer records to prevent unique index constraint violations
            DB::table('customers')
                ->whereIn('id', $deleteIds)
                ->delete();
        }

        // 3. Add composite unique index on salon_id and phone
        Schema::table('customers', function (Blueprint $table) {
            $table->unique(['salon_id', 'phone'], 'unique_salon_customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('unique_salon_customer_phone');
        });
    }
};
