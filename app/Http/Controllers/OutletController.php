<?php
namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\WashSlot;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::withCount([
            'technicians',
            'bookings',
            'bookings as today_bookings_count' => function ($query) {
                $query->whereDate('scheduled_at', today());
            }
        ])->get();

        $today = today()->toDateString();
        
        // Calculate available slots across all active outlets today
        $availableSlots = WashSlot::whereDate('slot_date', $today)
            ->where('status', 'available')
            ->whereHas('outlet', function ($q) {
                $q->where('status', 'active');
            })
            ->get()
            ->sum(function ($slot) {
                return max(0, $slot->capacity - $slot->booked_count);
            });

        // Calculate average slot utilization across active outlets
        $totalBooked = WashSlot::whereHas('outlet', function ($q) {
            $q->where('status', 'active');
        })->sum('booked_count');

        $totalCapacity = WashSlot::whereHas('outlet', function ($q) {
            $q->where('status', 'active');
        })->sum('capacity');

        $utilization = $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100) : 0;

        $stats = [
            'total'           => Outlet::count(),
            'active'          => Outlet::where('status', 'active')->count(),
            'available_slots' => $availableSlots,
            'utilization'     => $utilization . '%',
        ];
        return view('outlets.index', compact('outlets', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'address'            => 'required|string',
            'phone'              => 'nullable|string|max:20',
            'latitude'           => 'nullable|numeric',
            'longitude'          => 'nullable|numeric',
            'capacity_per_hour'  => 'required|integer|min:1|max:30',
            'open_time'          => 'required',
            'close_time'         => 'required',
        ]);
        $data['status'] = 'active';
        Outlet::create($data);
        return back()->with('success','Outlet berhasil ditambahkan.');
    }

    public function show(Outlet $outlet)
    {
        $outlet->load(['technicians','slots','bookings']);
        return view('outlets.show', compact('outlet'));
    }

    public function edit(Outlet $outlet)
    {
        return view('outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'address'           => 'required|string',
            'phone'             => 'nullable|string|max:20',
            'capacity_per_hour' => 'required|integer|min:1|max:30',
            'open_time'         => 'required',
            'close_time'        => 'required',
            'status'            => 'required|in:active,inactive,maintenance',
        ]);
        $outlet->update($data);
        return back()->with('success','Data outlet berhasil diperbarui.');
    }

    public function destroy(Outlet $outlet)
    {
        if ($outlet->bookings()->whereNotIn('status',['completed','cancelled'])->exists())
            return back()->with('error','Tidak dapat menghapus outlet dengan booking aktif.');
        $outlet->delete();
        return redirect('/outlets')->with('success','Outlet berhasil dihapus.');
    }
}
