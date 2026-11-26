<?php

namespace App\Services;

use App\Models\Salon;
use App\Models\User;
use App\Mail\SalonBackupMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SalonBackupService
{
    /**
     * All available backup module keys.
     */
    public const AVAILABLE_MODULES = [
        'profile_settings',
        'services',
        'packages',
        'memberships',
        'inventory',
        'customers',
        'bookings',
        'pos',
    ];

    /**
     * Export selected or all tenant data for a specific salon.
     *
     * @param int $salonId
     * @param array $selectedModules If empty, all modules are exported by default.
     * @return array
     */
    public function export(int $salonId, array $selectedModules = []): array
    {
        $salon = Salon::findOrFail($salonId);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $slug = $salon->slug ?: 'salon-' . $salon->id;
        $filename = "salon_backup_{$slug}_{$timestamp}.json";

        $dir = storage_path("app/backups/salons/{$salonId}");
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        $filePath = $dir . DIRECTORY_SEPARATOR . $filename;

        // If no modules specified or invalid, default to all available modules
        $activeModules = empty($selectedModules)
            ? self::AVAILABLE_MODULES
            : array_values(array_intersect($selectedModules, self::AVAILABLE_MODULES));

        if (empty($activeModules)) {
            $activeModules = self::AVAILABLE_MODULES;
        }

        $data = [];
        $recordCounts = [];

        // 1. Profile & Settings
        if (in_array('profile_settings', $activeModules)) {
            $data['salon'] = $salon->toArray();
            $data['settings'] = DB::table('settings')->where('salon_id', $salonId)->get()->toArray();
            $data['email_templates'] = DB::table('email_templates')->where('salon_id', $salonId)->get()->toArray();

            $recordCounts['settings'] = count($data['settings']);
            $recordCounts['email_templates'] = count($data['email_templates']);
        }

        // 2. Services & Categories
        if (in_array('services', $activeModules)) {
            $data['service_categories'] = DB::table('service_categories')->where('salon_id', $salonId)->get()->toArray();
            $data['services'] = DB::table('services')->where('salon_id', $salonId)->get()->toArray();

            $recordCounts['service_categories'] = count($data['service_categories']);
            $recordCounts['services'] = count($data['services']);
        }

        // 3. Service Packages & Service Relations
        if (in_array('packages', $activeModules)) {
            $data['packages'] = DB::table('packages')->where('salon_id', $salonId)->get()->toArray();
            $data['package_services'] = DB::table('package_services')
                ->whereIn('package_id', function ($q) use ($salonId) {
                    $q->select('id')->from('packages')->where('salon_id', $salonId);
                })
                ->get()
                ->toArray();
            $data['customer_package_balances'] = DB::table('customer_package_balances')->where('salon_id', $salonId)->get()->toArray();

            $recordCounts['packages'] = count($data['packages']);
            $recordCounts['package_services'] = count($data['package_services']);
            $recordCounts['package_balances'] = count($data['customer_package_balances']);
        }

        // 4. Memberships, Perks & Customer Memberships
        if (in_array('memberships', $activeModules)) {
            $data['memberships'] = DB::table('memberships')->where('salon_id', $salonId)->get()->toArray();
            $data['membership_services'] = DB::table('membership_services')
                ->whereIn('membership_id', function ($q) use ($salonId) {
                    $q->select('id')->from('memberships')->where('salon_id', $salonId);
                })
                ->get()
                ->toArray();
            $data['membership_inventory_items'] = DB::table('membership_inventory_items')
                ->whereIn('membership_id', function ($q) use ($salonId) {
                    $q->select('id')->from('memberships')->where('salon_id', $salonId);
                })
                ->get()
                ->toArray();
            $data['customer_memberships'] = DB::table('customer_memberships')->where('salon_id', $salonId)->get()->toArray();

            $recordCounts['memberships'] = count($data['memberships']);
            $recordCounts['membership_benefits'] = count($data['membership_services']) + count($data['membership_inventory_items']);
            $recordCounts['customer_memberships'] = count($data['customer_memberships']);
        }

        // 5. Inventory (Categories, Items, Transactions, Suppliers, Products)
        if (in_array('inventory', $activeModules)) {
            $data['inventory_categories'] = DB::table('inventory_categories')->where('salon_id', $salonId)->get()->toArray();
            $data['suppliers'] = DB::table('suppliers')->where('salon_id', $salonId)->get()->toArray();
            $data['inventory_items'] = DB::table('inventory_items')->where('salon_id', $salonId)->get()->toArray();
            $data['inventory_transactions'] = DB::table('inventory_transactions')
                ->whereIn('item_id', function ($q) use ($salonId) {
                    $q->select('id')->from('inventory_items')->where('salon_id', $salonId);
                })
                ->get()
                ->toArray();
            $data['products'] = DB::table('products')->where('salon_id', $salonId)->get()->toArray();
            $data['product_variants'] = DB::table('product_variants')->where('salon_id', $salonId)->get()->toArray();
            $data['service_inventory_items'] = DB::table('service_inventory_items')->where('salon_id', $salonId)->get()->toArray();

            $recordCounts['inventory_items'] = count($data['inventory_items']);
            $recordCounts['inventory_categories'] = count($data['inventory_categories']);
            $recordCounts['suppliers'] = count($data['suppliers']);
            $recordCounts['products'] = count($data['products']);
            $recordCounts['inventory_transactions'] = count($data['inventory_transactions']);
        }

        // 6. Customers & Client Preferences
        if (in_array('customers', $activeModules)) {
            $data['customers'] = DB::table('customers')->where('salon_id', $salonId)->get()->toArray();
            $data['customer_favorite_services'] = DB::table('customer_favorite_services')
                ->whereIn('customer_id', function ($q) use ($salonId) {
                    $q->select('id')->from('customers')->where('salon_id', $salonId);
                })
                ->get()
                ->toArray();

            $recordCounts['customers'] = count($data['customers']);
        }

        // 7. Bookings & Appointments
        if (in_array('bookings', $activeModules)) {
            $data['bookings'] = DB::table('bookings')->where('salon_id', $salonId)->get()->toArray();
            $recordCounts['bookings'] = count($data['bookings']);
        }

        // 8. POS Sales & Transactions
        if (in_array('pos', $activeModules)) {
            $data['pos_sales'] = DB::table('pos_sales')->where('salon_id', $salonId)->get()->toArray();
            $data['pos_sale_items'] = DB::table('pos_sale_items')->where('salon_id', $salonId)->get()->toArray();
            $data['pos_sale_item_consumables'] = DB::table('pos_sale_item_consumables')->where('salon_id', $salonId)->get()->toArray();
            $data['pos_refunds'] = DB::table('pos_refunds')->where('salon_id', $salonId)->get()->toArray();

            $recordCounts['pos_sales'] = count($data['pos_sales']);
            $recordCounts['pos_sale_items'] = count($data['pos_sale_items']);
            $recordCounts['pos_refunds'] = count($data['pos_refunds']);
        }

        // Build Payload
        $payload = [
            'version' => '2.0',
            'salon_id' => $salon->id,
            'salon_name' => $salon->name,
            'salon_slug' => $salon->slug,
            'exported_at' => now()->toIso8601String(),
            'selected_modules' => $activeModules,
            'record_counts' => $recordCounts,
            'data' => $data,
        ];

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $checksum = hash('sha256', $json);
        $payload['checksum'] = $checksum;

        $finalJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $sizeBytes = strlen($finalJson);
        $sizeMb = round($sizeBytes / 1024 / 1024, 2);

        // Enforce Plan Backup Size Limit
        if ($salon->isBackupSizeExceeded($sizeBytes)) {
            $limitMb = $salon->getBackupSizeLimitMb();
            throw new \OverflowException("Backup size ({$sizeMb} MB) exceeds your subscription plan limit of {$limitMb} MB. Please select fewer modules or upgrade your plan.");
        }

        File::put($filePath, $finalJson);

        Log::info("Salon backup created for salon #{$salonId} ({$salon->name}): {$filename} ({$sizeMb} MB) with modules: " . implode(', ', $activeModules));

        return [
            'filename' => $filename,
            'path' => $filePath,
            'size_bytes' => $sizeBytes,
            'size_formatted' => $sizeMb >= 1 ? "{$sizeMb} MB" : round($sizeBytes / 1024, 1) . ' KB',
            'checksum' => $checksum,
            'created_at' => now()->format('Y-m-d H:i:s'),
            'selected_modules' => $activeModules,
            'record_counts' => $recordCounts,
        ];
    }

    /**
     * Import and restore salon data from an uploaded backup JSON file.
     */
    public function import(int $salonId, string $jsonContent): array
    {
        $salon = Salon::findOrFail($salonId);
        $sizeBytes = strlen($jsonContent);

        // Enforce Plan Backup Size Limit
        if ($salon->isBackupSizeExceeded($sizeBytes)) {
            $limitMb = $salon->getBackupSizeLimitMb();
            $sizeMb = round($sizeBytes / 1024 / 1024, 2);
            throw new \OverflowException("The uploaded backup file ({$sizeMb} MB) exceeds your plan's maximum backup size limit of {$limitMb} MB.");
        }

        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['data'])) {
            throw new \InvalidArgumentException('Invalid backup file structure: missing data payload.');
        }

        $salon = Salon::findOrFail($salonId);
        $restoredCounts = [];

        DB::beginTransaction();
        try {
            // 1. Settings & Templates
            if (!empty($data['data']['settings'])) {
                $count = 0;
                foreach ($data['data']['settings'] as $item) {
                    $item = (array)$item;
                    if (isset($item['key'])) {
                        DB::table('settings')->updateOrInsert(
                            ['salon_id' => $salonId, 'key' => $item['key']],
                            ['value' => $item['value'] ?? '', 'updated_at' => now()]
                        );
                        $count++;
                    }
                }
                $restoredCounts['settings'] = $count;
            }

            if (!empty($data['data']['email_templates'])) {
                $count = 0;
                foreach ($data['data']['email_templates'] as $item) {
                    $item = (array)$item;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $type = $item['type'] ?? 'custom';
                    DB::table('email_templates')->updateOrInsert(
                        ['salon_id' => $salonId, 'type' => $type],
                        $item
                    );
                    $count++;
                }
                $restoredCounts['email_templates'] = $count;
            }

            // 2. Service Categories & Services
            $catMap = [];
            if (!empty($data['data']['service_categories'])) {
                $count = 0;
                foreach ($data['data']['service_categories'] as $item) {
                    $item = (array)$item;
                    $oldId = $item['id'] ?? null;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $existing = DB::table('service_categories')
                        ->where('salon_id', $salonId)
                        ->where('name', $item['name'] ?? '')
                        ->first();

                    if ($existing) {
                        $newId = $existing->id;
                    } else {
                        $newId = DB::table('service_categories')->insertGetId($item);
                    }
                    if ($oldId) $catMap[$oldId] = $newId;
                    $count++;
                }
                $restoredCounts['service_categories'] = $count;
            }

            $serviceMap = [];
            if (!empty($data['data']['services'])) {
                $count = 0;
                foreach ($data['data']['services'] as $item) {
                    $item = (array)$item;
                    $oldId = $item['id'] ?? null;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    if (!empty($item['category_id']) && isset($catMap[$item['category_id']])) {
                        $item['category_id'] = $catMap[$item['category_id']];
                    }
                    $item['updated_at'] = now();

                    $existing = DB::table('services')
                        ->where('salon_id', $salonId)
                        ->where('name', $item['name'] ?? '')
                        ->first();

                    if ($existing) {
                        $newId = $existing->id;
                        DB::table('services')->where('id', $newId)->update($item);
                    } else {
                        $newId = DB::table('services')->insertGetId($item);
                    }
                    if ($oldId) $serviceMap[$oldId] = $newId;
                    $count++;
                }
                $restoredCounts['services'] = $count;
            }

            // 3. Packages & Package Services
            $packageMap = [];
            if (!empty($data['data']['packages'])) {
                $count = 0;
                foreach ($data['data']['packages'] as $item) {
                    $item = (array)$item;
                    $oldId = $item['id'] ?? null;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $existing = DB::table('packages')
                        ->where('salon_id', $salonId)
                        ->where('name', $item['name'] ?? '')
                        ->first();

                    if ($existing) {
                        $newId = $existing->id;
                        DB::table('packages')->where('id', $newId)->update($item);
                    } else {
                        $newId = DB::table('packages')->insertGetId($item);
                    }
                    if ($oldId) $packageMap[$oldId] = $newId;
                    $count++;
                }
                $restoredCounts['packages'] = $count;
            }

            if (!empty($data['data']['package_services'])) {
                $count = 0;
                foreach ($data['data']['package_services'] as $ps) {
                    $ps = (array)$ps;
                    unset($ps['id']);
                    $oldPkgId = $ps['package_id'] ?? null;
                    $oldSvcId = $ps['service_id'] ?? null;

                    if ($oldPkgId && isset($packageMap[$oldPkgId])) {
                        $ps['package_id'] = $packageMap[$oldPkgId];
                        if ($oldSvcId && isset($serviceMap[$oldSvcId])) {
                            $ps['service_id'] = $serviceMap[$oldSvcId];
                        }
                        $ps['updated_at'] = now();

                        $exists = DB::table('package_services')
                            ->where('package_id', $ps['package_id'])
                            ->where('service_id', $ps['service_id'])
                            ->exists();

                        if (!$exists) {
                            DB::table('package_services')->insert($ps);
                            $count++;
                        }
                    }
                }
                $restoredCounts['package_services'] = $count;
            }

            // 4. Memberships & Membership Benefits
            $membershipMap = [];
            if (!empty($data['data']['memberships'])) {
                $count = 0;
                foreach ($data['data']['memberships'] as $item) {
                    $item = (array)$item;
                    $oldId = $item['id'] ?? null;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $existing = DB::table('memberships')
                        ->where('salon_id', $salonId)
                        ->where('name', $item['name'] ?? '')
                        ->first();

                    if ($existing) {
                        $newId = $existing->id;
                        DB::table('memberships')->where('id', $newId)->update($item);
                    } else {
                        $newId = DB::table('memberships')->insertGetId($item);
                    }
                    if ($oldId) $membershipMap[$oldId] = $newId;
                    $count++;
                }
                $restoredCounts['memberships'] = $count;
            }

            // 5. Customers
            $customerMap = [];
            if (!empty($data['data']['customers'])) {
                $count = 0;
                foreach ($data['data']['customers'] as $item) {
                    $item = (array)$item;
                    $oldId = $item['id'] ?? null;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $existing = null;
                    if (!empty($item['phone'])) {
                        $existing = DB::table('customers')->where('salon_id', $salonId)->where('phone', $item['phone'])->first();
                    } elseif (!empty($item['email'])) {
                        $existing = DB::table('customers')->where('salon_id', $salonId)->where('email', $item['email'])->first();
                    }

                    if ($existing) {
                        $newId = $existing->id;
                        DB::table('customers')->where('id', $newId)->update($item);
                    } else {
                        $newId = DB::table('customers')->insertGetId($item);
                    }
                    if ($oldId) $customerMap[$oldId] = $newId;
                    $count++;
                }
                $restoredCounts['customers'] = $count;
            }

            // 6. Suppliers & Inventory Items
            $supplierMap = [];
            if (!empty($data['data']['suppliers'])) {
                $count = 0;
                foreach ($data['data']['suppliers'] as $item) {
                    $item = (array)$item;
                    $oldId = $item['id'] ?? null;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $existing = DB::table('suppliers')
                        ->where('salon_id', $salonId)
                        ->where('name', $item['name'] ?? '')
                        ->first();

                    if ($existing) {
                        $newId = $existing->id;
                    } else {
                        $newId = DB::table('suppliers')->insertGetId($item);
                    }
                    if ($oldId) $supplierMap[$oldId] = $newId;
                    $count++;
                }
                $restoredCounts['suppliers'] = $count;
            }

            $invCatMap = [];
            if (!empty($data['data']['inventory_categories'])) {
                $count = 0;
                foreach ($data['data']['inventory_categories'] as $item) {
                    $item = (array)$item;
                    $oldId = $item['id'] ?? null;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $existing = DB::table('inventory_categories')
                        ->where('salon_id', $salonId)
                        ->where('name', $item['name'] ?? '')
                        ->first();

                    if ($existing) {
                        $newId = $existing->id;
                    } else {
                        $newId = DB::table('inventory_categories')->insertGetId($item);
                    }
                    if ($oldId) $invCatMap[$oldId] = $newId;
                    $count++;
                }
                $restoredCounts['inventory_categories'] = $count;
            }

            if (!empty($data['data']['inventory_items'])) {
                $count = 0;
                foreach ($data['data']['inventory_items'] as $item) {
                    $item = (array)$item;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    if (!empty($item['category_id']) && isset($invCatMap[$item['category_id']])) {
                        $item['category_id'] = $invCatMap[$item['category_id']];
                    }
                    if (!empty($item['supplier_id']) && isset($supplierMap[$item['supplier_id']])) {
                        $item['supplier_id'] = $supplierMap[$item['supplier_id']];
                    }
                    $item['updated_at'] = now();

                    $existing = DB::table('inventory_items')
                        ->where('salon_id', $salonId)
                        ->where('name', $item['name'] ?? '')
                        ->first();

                    if ($existing) {
                        DB::table('inventory_items')->where('id', $existing->id)->update($item);
                    } else {
                        DB::table('inventory_items')->insert($item);
                    }
                    $count++;
                }
                $restoredCounts['inventory_items'] = $count;
            }

            // 7. Products
            if (!empty($data['data']['products'])) {
                $count = 0;
                foreach ($data['data']['products'] as $item) {
                    $item = (array)$item;
                    unset($item['id']);
                    $item['salon_id'] = $salonId;
                    $item['updated_at'] = now();

                    $existing = DB::table('products')->where('salon_id', $salonId)->where('name', $item['name'] ?? '')->first();
                    if ($existing) {
                        DB::table('products')->where('id', $existing->id)->update($item);
                    } else {
                        DB::table('products')->insert($item);
                    }
                    $count++;
                }
                $restoredCounts['products'] = $count;
            }

            DB::commit();

            Log::info("Salon data imported successfully for salon #{$salonId}", $restoredCounts);

            return [
                'success' => true,
                'message' => 'Salon data restored successfully.',
                'restored' => $restoredCounts
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Salon import error for salon #{$salonId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send daily backup email with file attached to the salon admin.
     *
     * @param int $salonId
     * @param array $selectedModules
     * @return bool
     */
    public function sendDailyBackupEmail(int $salonId, array $selectedModules = []): bool
    {
        $salon = Salon::find($salonId);
        if (!$salon) return false;

        // Find primary salon admin or manager
        $admin = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['salon_admin', 'manager', 'admin']);
        })
            ->where('salon_id', $salonId)
            ->whereNotNull('email')
            ->first();

        if (!$admin && $salon->owner && $salon->owner->email) {
            $admin = $salon->owner;
        }

        if (!$admin || !$admin->email) {
            Log::warning("No recipient email found for backup of salon #{$salonId} ({$salon->name})");
            return false;
        }

        try {
            $backup = $this->export($salonId, $selectedModules);
            Mail::to($admin->email)->send(new SalonBackupMail($salon, $backup['path'], $backup['filename']));
            Log::info("Backup emailed to {$admin->email} for salon #{$salonId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to email backup for salon #{$salonId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * List backups for this salon.
     */
    public function listBackups(int $salonId): array
    {
        $dir = storage_path("app/backups/salons/{$salonId}");
        if (!File::isDirectory($dir)) {
            return [];
        }

        $files = File::files($dir);
        $list = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'json') continue;

            $bytes = $file->getSize();
            $sizeMb = round($bytes / 1024 / 1024, 2);

            $list[] = [
                'filename' => $file->getFilename(),
                'size_formatted' => $sizeMb >= 1 ? "{$sizeMb} MB" : round($bytes / 1024, 1) . ' KB',
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        usort($list, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return $list;
    }
}
