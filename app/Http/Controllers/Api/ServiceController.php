<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')
            ->select([
                'id',
                'name',
                'category_id',
                'price',
                'duration',
                'status',
                'description'
            ])
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'category' => $service->category ? [
                        'id' => $service->category->id,
                        'name' => $service->category->name
                    ] : null,
                    'price' => number_format($service->price, 2),
                    'duration' => $service->duration,
                    'status' => $service->status,
                    'description' => $service->description
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }
} 