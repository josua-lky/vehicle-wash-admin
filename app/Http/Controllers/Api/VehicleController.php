<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index(Request $request)
{
    $customer = $request->user();

    $vehicles = Vehicle::where(
        'customer_id',
        $customer->id
    )->get();

    return response()->json(
        $vehicles
    );
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'brand' => 'required',
        'model' => 'required',
        'license_plate' => 'required',
        'type' => 'required|in:roda_2,roda_4',
        'year' => 'nullable|integer|between:1901,2155',
        'color' => 'nullable|string',
        'notes' => 'nullable|string'
    ]);

    $vehicle = Vehicle::create([
        'customer_id' => $request->user()->id,
        'type' => $validated['type'],
        'brand' => $validated['brand'],
        'model' => $validated['model'],
        'license_plate' => strtoupper(
            $validated['license_plate']
        ),
        'year' => $validated['year'] ?? null,
        'color' => $validated['color'] ?? null,
        'notes' => $validated['notes'] ?? null
    ]);

    return response()->json([
        'vehicle' => $vehicle
    ]);
}

public function update(
    Request $request,
    Vehicle $vehicle
) {

    if (
        $vehicle->customer_id !==
        $request->user()->id
    ) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    $validated = $request->validate([
        'brand' => 'required',
        'model' => 'required',
        'license_plate' => 'required',
        'type' => 'required|in:roda_2,roda_4',
        'year' => 'nullable|integer|between:1901,2155',
        'color' => 'nullable|string',
        'notes' => 'nullable|string'
    ]);

    $vehicle->update([
        'type' => $validated['type'],
        'brand' => $validated['brand'],
        'model' => $validated['model'],
        'license_plate' => strtoupper(
            $validated['license_plate']
        ),
        'year' => $validated['year'] ?? null,
        'color' => $validated['color'] ?? null,
        'notes' => $validated['notes'] ?? null
    ]);

    return response()->json([
        'vehicle' => $vehicle
    ]);
}

    public function destroy(
        Request $request,
        Vehicle $vehicle
    ) {

        if (
            $vehicle->customer_id !==
            $request->user()->id
        ) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $vehicle->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}