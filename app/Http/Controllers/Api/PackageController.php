<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::where(
            'is_active',
            true
        )

        ->orderBy('sort_order')

        ->get();

        return response()->json(
            $packages
        );
    }
}