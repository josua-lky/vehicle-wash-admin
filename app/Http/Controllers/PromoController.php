<?php
namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoController extends Controller
{
    public function index()
    {
        $promos  = Promo::latest()->paginate(20);

        // This month queries
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalUsage = \App\Models\PromoUsage::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $totalDiscount = \App\Models\PromoUsage::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('discount_applied');

        $avgDiscount = \App\Models\PromoUsage::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->avg('discount_applied') ?? 0;

        $mostUsedPromo = \App\Models\PromoUsage::selectRaw('promo_id, count(*) as count')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('promo_id')
            ->orderByDesc('count')
            ->first();

        $mostUsedCode = '-';
        if ($mostUsedPromo) {
            $p = Promo::find($mostUsedPromo->promo_id);
            if ($p) {
                $mostUsedCode = $p->code;
            }
        }

        $newActivePromos = Promo::where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $stats = [
            'active_promos'     => Promo::where('status','active')->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>=',now()))->count(),
            'new_active_promos' => $newActivePromos,
            'total_usage'       => $totalUsage,
            'total_discount'    => $totalDiscount,
            'avg_discount'      => $avgDiscount,
            'most_used_code'    => $mostUsedCode,
        ];

        return view('promos.index', compact('promos','stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'code'            => 'nullable|string|max:20|unique:promos',
            'type'            => 'required|in:percentage,nominal,points',
            'value'           => 'required|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'max_usage'       => 'nullable|integer|min:1',
            'max_usage_per_user'=> 'nullable|integer|min:1',
            'starts_at'       => 'nullable|date',
            'expires_at'      => 'nullable|date|after_or_equal:starts_at',
            'description'     => 'nullable|string|max:500',
        ]);
        $data['code']   = strtoupper($data['code'] ?? Str::random(8));
        $data['status'] = 'active';
        $data['used_count'] = 0;
        Promo::create($data);
        return back()->with('success','Promo berhasil dibuat.');
    }

    public function update(Request $request, Promo $promo)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'value'           => 'required|numeric|min:0',
            'expires_at'      => 'nullable|date',
            'status'          => 'required|in:active,inactive',
            'description'     => 'nullable|string|max:500',
        ]);
        $promo->update($data);
        return back()->with('success','Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return back()->with('success','Promo berhasil dihapus.');
    }

    public function validatePromo(Request $request)
    {
        $request->validate(['code'=>'required','total'=>'required|numeric']);
        $promo = Promo::where('code', strtoupper($request->code))
                      ->where('status','active')
                      ->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>=',now()))
                      ->where(fn($q)=>$q->whereNull('min_transaction')->orWhere('min_transaction','<=',$request->total))
                      ->first();
        if (!$promo) return response()->json(['valid'=>false,'message'=>'Kode promo tidak valid atau sudah kedaluwarsa.'],422);
        $discount = $promo->type==='percentage' ? $request->total*$promo->value/100 : $promo->value;
        return response()->json(['valid'=>true,'discount'=>$discount,'promo'=>$promo]);
    }
}
