<?php
namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Outlet;
use App\Models\Booking;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index(Request $request)
    {
        $query = Technician::with('outlet')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%");
                });
            })
            ->when($request->status,         fn($q) => $q->where('status', $request->status))
            ->when($request->specialization, fn($q) => $q->where('specialization', $request->specialization));

        if ($request->sort === 'rating_asc') {
            $query->orderBy('rating');
        } else {
            $query->orderByDesc('rating');
        }

        $technicians = $query->paginate(15)->withQueryString();

        $stats = [
            'total'         => Technician::count(),
            'active_count'  => Technician::where('status', 'active')->count(),
            'active_orders' => Booking::whereIn('status', ['assigned','on_way','in_progress'])->count(),
            'avg_rating'    => number_format(Technician::avg('rating') ?? 0, 2),
        ];
        $outlets = Outlet::where('status','active')->get();
        return view('technicians.index', compact('technicians', 'stats', 'outlets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => 'required|string|max:20|unique:technicians',
            'email'          => 'required|email|unique:technicians',
            'password'       => 'required|string|min:6',
            'specialization' => 'required|string|in:motor,mobil',
            'outlet_id'      => 'nullable|exists:outlets,id',
        ]);
        $data['area']   = null;
        $data['status'] = 'active';
        $data['rating'] = 0.0;
        $data['password_plain'] = $data['password'];
        $data['password'] = bcrypt($data['password']);
        Technician::create($data);
        return back()->with('success', 'Teknisi berhasil ditambahkan.');
    }

    public function show(Technician $technician)
    {
        $technician->load('bookings.customer');
        return view('technicians.show', compact('technician'));
    }

    public function edit(Technician $technician)
    {
        $outlets = Outlet::where('status','active')->get();
        return view('technicians.edit', compact('technician', 'outlets'));
    }

    public function update(Request $request, Technician $technician)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => "required|string|max:20|unique:technicians,phone,{$technician->id}",
            'email'          => "required|email|unique:technicians,email,{$technician->id}",
            'password'       => 'nullable|string|min:6',
            'specialization' => 'required|string|in:motor,mobil',
            'area'           => 'nullable|string|max:100',
            'outlet_id'      => 'nullable|exists:outlets,id',
            'status'         => 'required|in:active,inactive,cuti,busy',
        ]);
        if (!empty($data['password'])) {
            $data['password_plain'] = $data['password'];
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $technician->update($data);
        return back()->with('success', 'Data teknisi berhasil diperbarui.');
    }

    public function destroy(Technician $technician)
    {
        $technician->delete();
        return redirect('/technicians')->with('success', 'Teknisi berhasil dihapus.');
    }
}
