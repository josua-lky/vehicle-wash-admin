<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'bookings_today'     => Booking::whereDate('created_at', today())->count(),
            'active_clients'     => Customer::where('status', 'active')->count(),
            'total_revenue'      => Payment::where('status', 'paid')->whereMonth('paid_at', now()->month)->sum('amount'),
            'active_technicians' => Technician::where('status', 'active')->count(),
        ];

        $recentBookings = Booking::with('customer')->latest()->limit(6)->get()
            ->map(fn($b) => [
                'id'       => $b->id, 'code'     => $b->booking_code,
                'customer' => $b->customer->name ?? '—',
                'vehicle'  => $b->vehicle_name   ?? '—',
                'status'   => $b->status,
                'time'     => optional($b->scheduled_at)->format('d M, H:i') ?? '—',
            ]);

        return view('dashboard.index', compact('stats', 'recentBookings'));
    }

    public function search(Request $request)
    {
        $q = $request->q;
        if (empty($q)) {
            return redirect('/dashboard');
        }

        $bookings = Booking::with('customer')
            ->where('booking_code', 'like', "%{$q}%")
            ->orWhere('vehicle_name', 'like', "%{$q}%")
            ->orWhereHas('customer', fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"))
            ->latest()->limit(10)->get();

        $technicians = Technician::where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->latest()->limit(10)->get();

        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->latest()->limit(10)->get();

        return view('dashboard.search', compact('q', 'bookings', 'technicians', 'customers'));
    }
}
