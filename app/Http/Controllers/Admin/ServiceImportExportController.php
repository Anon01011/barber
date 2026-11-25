<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ServiceImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;
use League\Csv\Reader;
use SplTempFileObject;

class ServiceImportExportController extends Controller
{
    protected $importService;

    public function __construct(ServiceImportService $importService)
    {
        $this->importService = $importService;
        $this->middleware(['auth', 'role:super_admin|salon_admin|manager']);
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $data = ServiceImportService::getTemplateHeaders();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        foreach ($data as $row) {
            $csv->insertOne($row);
        }

        return Response::make($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="service_import_template.csv"',
        ]);
    }

    /**
     * Import services from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0);

            $records = [];
            foreach ($csv->getRecords() as $record) {
                $records[] = $record;
            }

            $salonId = $this->getSalonId();
            $results = $this->importService->import($records, $salonId);

            if ($results['failed'] > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Import completed with errors. {$results['success']} services imported successfully, {$results['failed']} failed.",
                    'results' => $results
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "{$results['success']} services imported successfully!",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export all services to CSV including assigned backbar recipes/consumable products.
     */
    public function export()
    {
        $salonId = $this->getSalonId();
        
        $services = \App\Models\Service::where('salon_id', $salonId)
            ->with(['category', 'recipeItems.inventoryItem'])
            ->get();

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        // Insert headers matching the import template format
        $csv->insertOne([
            'Category Name', 
            'Category Description', 
            'Service Name', 
            'Service Description', 
            'Price', 
            'Duration (mins)', 
            'Status', 
            'Online Booking',
            'Assigned Products (Recipe: SKU:Qty:Unit:Chargeable:ExtraPrice)'
        ]);

        $sanitizeRow = function (array $row) {
            return array_map(function ($val) {
                if (is_string($val) && strlen($val) > 0 && in_array($val[0], ['=', '+', '-', '@', "\t", "\r"])) {
                    return "'" . $val;
                }
                return $val;
            }, $row);
        };

        foreach ($services as $service) {
            $recipeParts = [];
            if ($service->recipeItems) {
                foreach ($service->recipeItems as $recipe) {
                    $sku = $recipe->inventoryItem?->sku ?? $recipe->inventoryItem?->name;
                    if ($sku) {
                        $isCharge = $recipe->is_billable_extra ? 'yes' : 'no';
                        $extraPrice = number_format((float)($recipe->extra_price_per_unit ?? 0), 2, '.', '');
                        $recipeParts[] = "{$sku}:{$recipe->quantity}:{$recipe->unit}:{$isCharge}:{$extraPrice}";
                    }
                }
            }
            $recipeString = implode(' | ', $recipeParts);

            $csv->insertOne($sanitizeRow([
                $service->category->name ?? '',
                $service->category->description ?? '',
                $service->name,
                $service->description ?? '',
                number_format($service->price, 2, '.', ''),
                $service->duration,
                $service->status,
                $service->available_for_online_booking ? 'yes' : 'no',
                $recipeString
            ]));
        }

        return Response::make($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="services_export.csv"',
        ]);
    }

    /**
     * Resolve current salon ID safely across all authentication and tenant contexts.
     */
    protected function getSalonId(): int
    {
        $salonId = app()->bound('current_salon') && app('current_salon')
            ? app('current_salon')->id
            : (auth()->user()?->salon_id ?? session('current_salon_id'));

        if (!$salonId && request()->route('salon_slug')) {
            $salon = \App\Models\Salon::where('slug', request()->route('salon_slug'))->first();
            $salonId = $salon?->id;
        }

        if (!$salonId) {
            abort(404, 'Salon context not found.');
        }

        return (int) $salonId;
    }
}
