<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            $customers = Customer::select('id', 'name', 'email', 'phone')
                ->orderBy('name')
                ->get();

            return response()->json($customers);
        } catch (\Exception $e) {
            Log::error('API: Error fetching customers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch customers'], 500);
        }
    }
}