<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class ServiceImportService
{
    /**
     * Import services and categories from array data
     */
    public function import(array $data, int $salonId): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and row 1 is header

                try {
                    $this->importRow($row, $salonId, $rowNumber);
                    $results['success']++;
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'message' => $e->getMessage()
                    ];
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * Import a single row
     */
    protected function importRow(array $row, int $salonId, int $rowNumber): void
    {
        // Normalize keys (handle spaces, parentheses, and case)
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim($key));
            $cleanKey = str_replace([' ', '(', ')', '-'], '_', $cleanKey);
            $cleanKey = preg_replace('/__+/', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');
            $normalizedRow[$cleanKey] = $value;
        }

        // Validate required fields
        $validator = Validator::make($normalizedRow, [
            'category_name' => 'required|string|max:255',
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_mins' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new Exception("Validation failed: " . implode(', ', $validator->errors()->all()));
        }

        // 1. Find or create category
        $category = ServiceCategory::where('salon_id', $salonId)
            ->where('name', $normalizedRow['category_name'])
            ->first();

        $salon = \App\Models\Salon::find($salonId);

        if (!$category) {
            if (!$salon->canAddServiceCategory()) {
                throw new Exception("Limit reached: You cannot add more service categories under your current plan.");
            }

            $category = ServiceCategory::create([
                'salon_id' => $salonId,
                'name' => $normalizedRow['category_name'],
                'description' => $normalizedRow['category_description'] ?? null,
                'status' => 'active',
            ]);
        }

        // 2. Check if service already exists in this category
        $existingService = Service::where('salon_id', $salonId)
            ->where('category_id', $category->id)
            ->where('name', $normalizedRow['service_name'])
            ->first();

        $serviceData = [
            'salon_id' => $salonId,
            'category_id' => $category->id,
            'name' => $normalizedRow['service_name'],
            'description' => $normalizedRow['service_description'] ?? null,
            'price' => $normalizedRow['price'],
            'duration' => $normalizedRow['duration_mins'],
            'status' => $this->parseStatus($normalizedRow['status'] ?? 'active'),
            'available_for_online_booking' => $this->parseBoolean($normalizedRow['online_booking'] ?? 'yes'),
        ];

        if ($existingService) {
            $existingService->update($serviceData);
        } else {
            if (!$salon->canAddService()) {
                throw new Exception("Limit reached: You cannot add more services under your current plan.");
            }
            Service::create($serviceData);
        }
    }

    /**
     * Parse status value
     */
    protected function parseStatus($value): string
    {
        $value = strtolower(trim($value));
        return in_array($value, ['active', 'inactive']) ? $value : 'active';
    }

    /**
     * Parse boolean values
     */
    protected function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'active']);
    }

    /**
     * Get template headers
     */
    public static function getTemplateHeaders(): array
    {
        return [
            ['Category Name', 'Category Description', 'Service Name', 'Service Description', 'Price', 'Duration (mins)', 'Status', 'Online Booking'],
            ['Haircut', 'All types of haircuts', 'Men\'s Haircut', 'Standard men\'s haircut', '25.00', '30', 'active', 'yes'],
            ['Haircut', 'All types of haircuts', 'Women\'s Haircut', 'Standard women\'s haircut', '45.00', '60', 'active', 'yes'],
        ];
    }
}
