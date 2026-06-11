<?php
namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($request->vehicle_type, fn($q) => $q->where('vehicle_type', $request->vehicle_type))
            ->when($request->status !== null, function ($q) use ($request) {
                $status = $request->status === 'active' ? true : false;
                $q->where('is_active', $status);
            });

        if ($request->sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($request->sort === 'price_desc') {
            $query->orderByDesc('price');
        } else {
            $query->orderBy('sort_order');
        }

        $packages = $query->paginate(15)->withQueryString();

        $stats = [
            'total'          => Package::count(),
            'active_count'   => Package::where('is_active', true)->count(),
            'roda_2_count'   => Package::where('vehicle_type', 'roda_2')->count(),
            'roda_4_count'   => Package::where('vehicle_type', 'roda_4')->count(),
        ];

        return view('packages.index', compact('packages', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string',
            'vehicle_type'     => 'required|in:roda_2,roda_4,all',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = true;
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        Package::create($data);
        return back()->with('success', 'Paket layanan berhasil ditambahkan.');
    }

    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string',
            'vehicle_type'     => 'required|in:roda_2,roda_4,all',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'sort_order'       => 'required|integer|min:0',
            'is_active'        => 'required|boolean',
        ]);

        $package->update($data);
        return back()->with('success', 'Data paket layanan berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        if ($package->bookings()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
            return back()->with('error', 'Tidak dapat menghapus paket dengan booking aktif.');
        }

        $package->delete();
        return redirect('/packages')->with('success', 'Paket layanan berhasil dihapus.');
    }
}
