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

            $salonId = auth()->user()->salon_id;
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
     * Export all services to CSV
     */
    public function export()
    {
        $salonId = auth()->user()->salon_id;
        
        $services = \App\Models\Service::where('salon_id', $salonId)
            ->with('category')
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
            'Online Booking'
        ]);

        foreach ($services as $service) {
            $csv->insertOne([
                $service->category->name ?? '',
                $service->category->description ?? '',
                $service->name,
                $service->description ?? '',
                number_format($service->price, 2, '.', ''),
                $service->duration,
                $service->status,
                $service->available_for_online_booking ? 'yes' : 'no'
            ]);
        }

        return Response::make($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="services_export.csv"',
        ]);
    }
}
