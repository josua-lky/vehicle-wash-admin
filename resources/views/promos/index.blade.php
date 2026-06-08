@extends('layouts.app')
@section('title', 'Promo')

@section('content')
<div class="p-6 space-y-5" x-data="promoPage()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Promos</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola program promo dan voucher</p>
        </div>
        <button @click="showAddPromo=true"
                class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg shadow"
                style="background:#F0C419; color:#1B2337;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Promo Baru
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex justify-between items-center">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Total Active Promos</p>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['active_promos'] ?? 12 }}</p>
            </div>
            <span class="badge badge-green">+3 baru aktif</span>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- LEFT: Active Programs --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Active Promos List --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Active Programs</h3>
                    <div class="flex gap-2">
                        <select class="text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-600 bg-slate-50">
                            <option>Semua Tipe</option>
                            <option>Persentase</option>
                            <option>Nominal</option>
                        </select>
                    </div>
                </div>

                <div class="divide-y divide-slate-50">
                    @php
                    $promos = $promos ?? collect([
                        ['id'=>1,'name'=>'Weekend Full Wash','code'=>'WFW20','type'=>'percentage','value'=>20,'min_trx'=>50000,'used'=>145,'max_use'=>500,'expires'=>'31 Okt 2024','status'=>'active','description'=>'Diskon 20% untuk semua paket cuci di akhir pekan'],
                        ['id'=>3,'name'=>'Referral Bonus','code'=>'REFER50','type'=>'nominal','value'=>50000,'min_trx'=>0,'used'=>312,'max_use'=>null,'expires'=>null,'status'=>'active','description'=>'Bonus Rp 50.000 untuk setiap referral berhasil'],
                        ['id'=>4,'name'=>'First Wash Free','code'=>'FIRST50','type'=>'percentage','value'=>50,'min_trx'=>0,'used'=>672,'max_use'=>1000,'expires'=>'31 Des 2024','status'=>'active','description'=>'Diskon 50% untuk pengguna baru'],
                        ['id'=>5,'name'=>'Fleet Discount','code'=>'FLEET30','type'=>'percentage','value'=>30,'min_trx'=>200000,'used'=>28,'max_use'=>200,'expires'=>'28 Feb 2025','status'=>'active','description'=>'Diskon 30% untuk pemesanan fleet (min. Rp 200.000)'],
                    ]);
                    @endphp
                    @foreach($promos as $promo)
                    @php
                        if (is_array($promo)) {
                            $prId = $promo['id'] ?? '';
                            $prName = $promo['name'] ?? '-';
                            $prCode = $promo['code'] ?? '';
                            $prType = $promo['type'] ?? 'percentage';
                            $prValue = $promo['value'] ?? 0;
                            $prMinTrx = $promo['min_trx'] ?? ($promo['min_transaction'] ?? 0);
                            $prUsed = $promo['used'] ?? ($promo['used_count'] ?? 0);
                            $prMaxUse = $promo['max_use'] ?? ($promo['max_usage'] ?? null);
                            $prExpires = $promo['expires'] ?? ($promo['expires_at'] ?? null);
                            if ($prExpires instanceof \Carbon\Carbon) {
                                $prExpires = $prExpires->format('d M Y');
                            }
                            $prStatus = $promo['status'] ?? 'active';
                            $prDescription = $promo['description'] ?? '';
                        } else {
                            $prId = $promo->id;
                            $prName = $promo->name;
                            $prCode = $promo->code;
                            $prType = $promo->type;
                            $prValue = (float)$promo->value;
                            $prMinTrx = (float)$promo->min_transaction;
                            $prUsed = $promo->used_count;
                            $prMaxUse = $promo->max_usage;
                            $prExpires = $promo->expires_at ? $promo->expires_at->format('d M Y') : null;
                            $prStatus = $promo->status;
                            $prDescription = $promo->description;
                        }
                        $displayValue = ($prType === 'nominal' && $prValue >= 1000) ? ($prValue / 1000) : $prValue;
                        $prArray = [
                            'id' => $prId,
                            'name' => $prName,
                            'code' => $prCode,
                            'type' => $prType,
                            'value' => $prValue,
                            'min_transaction' => $prMinTrx,
                            'used_count' => $prUsed,
                            'max_usage' => $prMaxUse,
                            'expires_at' => $prExpires,
                            'status' => $prStatus,
                            'description' => $prDescription
                        ];
                    @endphp
                    <div class="px-6 py-4 hover:bg-slate-50 transition-colors group">
                        <div class="flex items-start gap-4">
                            {{-- Discount badge --}}
                            <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex flex-col items-center justify-center text-white"
                                 style="background:linear-gradient(135deg,#1B2337,#2D3D5E);">
                                <span class="text-lg font-black" style="color:#F0C419;">{{ $displayValue }}{{ $prType==='percentage'?'%':($prType==='nominal'?'K':'x') }}</span>
                                <span class="text-xs opacity-60">OFF</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-slate-800 text-sm">{{ $prName }}</h4>
                                    <span class="badge badge-green">Aktif</span>
                                    @if($prExpires)
                                    <span class="text-xs text-slate-400">Sampai {{ $prExpires }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mb-2">{{ $prDescription }}</p>
                                <div class="flex items-center gap-4 text-xs text-slate-500">
                                    <span class="font-mono font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">{{ $prCode }}</span>
                                    <span>Digunakan: <strong class="text-slate-700">{{ $prUsed }}</strong>{{ $prMaxUse ? '/'.$prMaxUse : '' }}x</span>
                                    @if($prMinTrx > 0)<span>Min: Rp {{ number_format($prMinTrx,0,',','.') }}</span>@endif
                                </div>
                                @if($prMaxUse)
                                <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full" style="width:{{ min(100,round($prUsed/$prMaxUse*100)) }}%; background:#F0C419;"></div>
                                </div>
                                @endif
                            </div>

                            <div class="flex-shrink-0 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="editPromo({{ json_encode($prArray) }})"
                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="/promos/{{ $prId }}" onsubmit="return confirm('Hapus promo ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT: New Campaign Form --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-semibold text-slate-800 mb-4">New Campaign</h3>

                <form method="POST" action="/promos" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Campaign Title</label>
                        <input type="text" name="name" placeholder="Nama promo..."
                               class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Kode Voucher</label>
                        <div class="flex gap-2">
                            <input type="text" name="code" x-model="promoCode" placeholder="KODE123"
                                   class="flex-1 px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 font-mono uppercase">
                            <button type="button" @click="generateCode()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-600 hover:bg-slate-50">
                                Generate
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Tipe Diskon</label>
                            <select name="type" x-model="discountType" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                                <option value="percentage">Persentase (%)</option>
                                <option value="nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nilai</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs" x-text="discountType==='percentage'?'%':'Rp'"></span>
                                <input type="number" name="value" placeholder="20"
                                       class="w-full pl-7 pr-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Mulai</label>
                            <input type="date" name="starts_at" value="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Berakhir</label>
                            <input type="date" name="expires_at"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Min. Transaksi (Rp)</label>
                        <input type="number" name="min_transaction" placeholder="0 = tidak ada minimum"
                               class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">Target Audience</label>
                        <div class="space-y-2">
                            @foreach(['Semua Pelanggan'=>'all','Pelanggan Baru'=>'new'] as $label=>$val)
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="target[]" value="{{ $val }}"
                                       class="w-4 h-4 rounded" style="accent-color:#F0C419;">
                                <span class="text-sm text-slate-600">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" class="flex-1 py-2.5 rounded-xl text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50">
                            Save as Draft
                        </button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl text-xs font-semibold text-slate-900"
                                style="background:#F0C419;">
                            Launch Campaign
                        </button>
                    </div>
                </form>
            </div>

            {{-- Quick stats --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Statistik Promo Bulan Ini</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Total Penggunaan Voucher</span><span class="font-semibold text-slate-800">1,247x</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Total Diskon Diberikan</span><span class="font-semibold text-slate-800">Rp 4,2 jt</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Rata-rata Diskon/Transaksi</span><span class="font-semibold text-slate-800">Rp 18.400</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Promo Paling Banyak Dipakai</span><span class="font-semibold" style="color:#F0C419;">FIRST50</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD PROMO MODAL --}}
    <div x-show="showAddPromo" x-cloak class="fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4" @click.self="showAddPromo=false">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800" x-text="editingPromo?'Edit Promo':'Buat Promo Baru'"></h3>
                <button @click="showAddPromo=false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-500">Gunakan form campaign di sebelah kanan untuk membuat promo baru, atau klik edit pada promo yang sudah ada.</p>
                <button @click="showAddPromo=false" class="mt-4 w-full py-2.5 rounded-xl text-sm font-semibold text-slate-900" style="background:#F0C419;">OK</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function promoPage() {
    return {
        showAddPromo: false,
        editingPromo: null,
        promoCode: '',
        discountType: 'percentage',
        generateCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            this.promoCode = Array.from({length: 8}, () => chars[Math.floor(Math.random()*chars.length)]).join('');
        },
        editPromo(promo) { this.editingPromo = promo; this.showAddPromo = true; }
    }
}
</script>
@endpush
