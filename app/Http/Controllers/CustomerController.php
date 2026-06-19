<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('bookings')->with('bookings');

        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        });

        $query->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        if ($request->sort === 'orders') {
            $query->orderBy('bookings_count', 'desc');
        } elseif ($request->sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->latest();
        }

        $customers = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => Customer::count(),
            'active'   => Customer::where('status','active')->count(),
            'new'      => Customer::whereMonth('created_at', now()->month)->count(),
            'inactive' => Customer::where('status','inactive')->count(),
        ];
        $totalCustomers = Customer::count();
        return view('customers.index', compact('customers','stats','totalCustomers'));
    }

    public function show(Customer $customer)
    {
        $customer->load('bookings.package','vehicles');
        return view('customers.show', compact('customer'));
    }

    public function toggleStatus(Customer $customer)
    {
        $newStatus = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->update(['status' => $newStatus]);
        
        if ($newStatus === 'inactive') {
            $customer->tokens()->delete();
        }
        
        return back()->with('success','Status pelanggan berhasil diubah.');
    }

    public function export()
    {
        $customers = Customer::latest()->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=customers-export.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Pelanggan', 'Nama', 'Email', 'No Telepon', 'Status', 'Tanggal Bergabung']);

            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email ?? '—',
                    $customer->phone ?? '—',
                    $customer->status,
                    $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : '—',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
