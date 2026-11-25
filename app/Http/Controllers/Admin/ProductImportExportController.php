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
            
            $salonId = $this->getSalonId();
            $branchId = $this->getBranchId();
            
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
        $salonId = $this->getSalonId();
        $branchId = $this->getBranchId();
        
        $data = $this->exportService->export($salonId, $branchId);
        
        $filename = 'products_export_' . date('Y-m-d_His') . '.csv';
        
        return $this->generateCsvResponse($data, $filename);
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

    /**
     * Resolve current branch ID safely.
     */
    protected function getBranchId(): ?int
    {
        return session('current_branch_id') ?? auth()->user()?->branch_id;
    }

    /**
     * Generate CSV response
     */
    protected function generateCsvResponse(array $data, string $filename)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        foreach ($data as $row) {
            $sanitizedRow = array_map(function ($val) {
                if (is_string($val) && strlen($val) > 0 && in_array($val[0], ['=', '+', '-', '@', "\t", "\r"])) {
                    return "'" . $val;
                }
                return $val;
            }, (array) $row);
            $csv->insertOne($sanitizedRow);
        }
        
        return Response::make($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
