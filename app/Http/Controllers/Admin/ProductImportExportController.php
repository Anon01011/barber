<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductImportService;
use App\Services\ProductExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;
use League\Csv\Reader;
use SplTempFileObject;

class ProductImportExportController extends Controller
{
    protected $importService;
    protected $exportService;

    public function __construct(ProductImportService $importService, ProductExportService $exportService)
    {
        $this->importService = $importService;
        $this->exportService = $exportService;
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $template = ProductExportService::getTemplate();
        
        return $this->generateCsvResponse($template, 'product_import_template.csv');
    }

    /**
     * Import products from CSV
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
            
            // Get original headers and create normalization map
            $headers = $csv->getHeader();
            $headerMap = [];
            foreach ($headers as $header) {
                // Remove BOM if present (UTF-8 BOM is EF BB BF)
                $cleanHeader = preg_replace('/^\xEF\xBB\xBF/', '', $header);
                $cleanHeader = strtolower(trim($cleanHeader));
                $headerMap[$header] = $cleanHeader;
            }
            
            // Process records with normalized keys
            $records = [];
            foreach ($csv->getRecords() as $record) {
                $normalizedRecord = [];
                foreach ($record as $key => $value) {
                    if (isset($headerMap[$key])) {
                        $normalizedRecord[$headerMap[$key]] = $value;
                    }
                }
                $records[] = $normalizedRecord;
            }
            
            $salonId = auth()->user()->salon_id;
            $branchId = auth()->user()->branch_id;
            
            $results = $this->importService->import($records, $salonId, $branchId);
            
            if ($results['failed'] > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Import completed with errors. {$results['success']} products imported successfully, {$results['failed']} failed.",
                    'results' => $results
                ], 422);
            }
            
            return response()->json([
                'success' => true,
                'message' => "{$results['success']} products imported successfully!",
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
     * Export products to CSV
     */
    public function export()
    {
        $salonId = auth()->user()->salon_id;
        $branchId = auth()->user()->branch_id;
        
        $data = $this->exportService->export($salonId, $branchId);
        
        $filename = 'products_export_' . date('Y-m-d_His') . '.csv';
        
        return $this->generateCsvResponse($data, $filename);
    }

    /**
     * Generate CSV response
     */
    protected function generateCsvResponse(array $data, string $filename)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        foreach ($data as $row) {
            $csv->insertOne($row);
        }
        
        return Response::make($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
