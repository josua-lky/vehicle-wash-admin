<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Technician;
use App\Models\Outlet;
use App\Models\Customer;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from    = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to      = $request->date_to   ?? now()->toDateString();
        $outlets = Outlet::where('status','active')->get();

        $stats = [
            'monthly_revenue' => Payment::where('status','paid')->whereBetween('paid_at',[$from.' 00:00:00',$to.' 23:59:59'])->sum('amount'),
            'orders_served'   => Booking::where('status','completed')->whereBetween('created_at',[$from.' 00:00:00',$to.' 23:59:59'])->count(),
            'avg_per_order'   => Booking::where('status','completed')->whereBetween('created_at',[$from,$to])->avg('total_amount') ?? 0,
            'satisfaction'    => 98.2,
        ];
        return view('reports.index', compact('stats','outlets','from','to'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format','pdf');
        if ($format === 'pdf') {
            // barryvdh/laravel-dompdf
            return response()->json(['message'=>'Install barryvdh/laravel-dompdf: composer require barryvdh/laravel-dompdf']);
        }
        // maatwebsite/excel
        return response()->json(['message'=>'Install maatwebsite/excel: composer require maatwebsite/excel']);
    }
}
