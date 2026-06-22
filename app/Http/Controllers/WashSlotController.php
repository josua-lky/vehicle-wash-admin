<?php
namespace App\Http\Controllers;

use App\Models\WashSlot;
use App\Models\Outlet;
use Illuminate\Http\Request;

class WashSlotController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::where('status','active')->get();
        $selectedOutlet = $request->outlet_id ? Outlet::find($request->outlet_id) : null;
        $slots = WashSlot::whereHas('outlet', fn($q)=>$q->where('status','active'))
                         ->when($request->outlet_id, fn($q)=>$q->where('outlet_id',$request->outlet_id))
                         ->with('outlet')->orderBy('slot_date')->orderBy('slot_time')->get();
        $stats = [
            'total'            => $slots->count(),
            'available_slots'  => $slots->filter(fn($s) => $s->status === 'available' && $s->booked_count < $s->capacity)->count(),
            'booked'           => $slots->filter(fn($s) => $s->status === 'available' && $s->booked_count > 0)->count(),
            'blocked'          => $slots->where('status','blocked')->count(),
        ];

        $todaySlots = $slots->filter(fn($s) => \Carbon\Carbon::parse($s->slot_date)->isToday());
        $todayStats = [
            'capacity' => $todaySlots->sum('capacity'),
            'booked' => $todaySlots->sum('booked_count'),
        ];

        $customers = \App\Models\Customer::where('status', 'active')->get();
        $packages = \App\Models\Package::where('is_active', true)->get();

        $bookings = \App\Models\Booking::with(['customer', 'package', 'outlet'])
            ->where('service_type', 'outlet')
            ->whereHas('outlet', fn($q) => $q->where('status', 'active'))
            ->when($request->outlet_id, fn($q) => $q->where('outlet_id', $request->outlet_id))
            ->orderBy('scheduled_at')
            ->get();

        $bookingsData = $bookings->map(function ($b) {
            return [
                'id' => $b->id,
                'booking_code' => $b->booking_code,
                'customer_name' => $b->customer->name ?? '-',
                'vehicle_name' => $b->vehicle_name,
                'vehicle_type' => $b->vehicle_type,
                'scheduled_date' => $b->scheduled_at->toDateString(),
                'scheduled_time' => $b->scheduled_at->format('H:i'),
                'scheduled_at' => $b->scheduled_at->format('Y-m-d H:i:s'),
                'status' => $b->status,
                'status_label' => $b->status_label,
                'outlet_id' => $b->outlet_id,
                'outlet_name' => $b->outlet->name ?? '-'
            ];
        });

        return view('slots.index', compact('outlets','slots','stats','selectedOutlet', 'customers', 'packages', 'bookingsData', 'todayStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id'  => 'required|exists:outlets,id',
            'slot_date'  => 'required|date|after_or_equal:today',
            'slot_time'  => 'required',
            'capacity'   => 'required|integer|min:1|max:20',
        ]);
        WashSlot::firstOrCreate(
            ['outlet_id'=>$request->outlet_id,'slot_date'=>$request->slot_date,'slot_time'=>$request->slot_time],
            ['capacity'=>$request->capacity,'booked_count'=>0,'status'=>'available']
        );
        return back()->with('success','Slot berhasil ditambahkan.');
    }

    public function destroy(WashSlot $washSlot)
    {
        if ($washSlot->booked_count > 0)
            return back()->with('error','Tidak dapat menghapus slot yang sudah ada booking.');
        $washSlot->delete();
        return back()->with('success','Slot berhasil dihapus.');
    }

    public function available(Request $request)
    {
        $request->validate(['outlet_id'=>'required|exists:outlets,id','date'=>'required|date']);
        $slots = WashSlot::where('outlet_id',$request->outlet_id)
                         ->whereDate('slot_date',$request->date)
                         ->where('status','available')
                         ->whereRaw('booked_count < capacity')
                         ->get(['id','slot_time','capacity','booked_count']);
        return response()->json($slots);
    }
}
