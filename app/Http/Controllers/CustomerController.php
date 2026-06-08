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
        $customer->update(['status' => $customer->status==='active' ? 'inactive' : 'active']);
        return back()->with('success','Status pelanggan berhasil diubah.');
    }

    public function export()
    {
        return response()->json(['message'=>'Install maatwebsite/excel for export.']);
    }
}
