@extends('layouts.app')
@section('title', 'Manajemen Paket Layanan')

@section('content')
<div class="p-6 space-y-5" x-data="packagePage()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Paket Layanan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola paket cuci, detailing, harga, dan durasi pengerjaan.</p>
        </div>
        <button @click="showAddModal=true"
                class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg shadow"
                style="background:#F0C419; color:#1B2337;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Paket
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Total Paket</p>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Paket Aktif</p>
            <p class="text-3xl font-bold text-emerald-600">{{ $stats['active_count'] ?? 0 }}</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Khusus Roda 2</p>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['roda_2_count'] ?? 0 }}</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Khusus Roda 4</p>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['roda_4_count'] ?? 0 }}</p>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- Search + Filters --}}
        <form method="GET" action="/packages" class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-40">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari paket..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
            </div>
            <select name="vehicle_type" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-500 bg-slate-50">
                <option value="">Semua Tipe Kendaraan</option>
                <option value="roda_2" {{ request('vehicle_type') === 'roda_2' ? 'selected' : '' }}>Roda 2 (Motor)</option>
                <option value="roda_4" {{ request('vehicle_type') === 'roda_4' ? 'selected' : '' }}>Roda 4 (Mobil)</option>
                <option value="all" {{ request('vehicle_type') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-500 bg-slate-50">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <select name="sort" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-500 bg-slate-50">
                <option value="sort_order" {{ request('sort') === 'sort_order' ? 'selected' : '' }}>Urutan Sort Order</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Nama Paket</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Tipe Kendaraan</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Durasi</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Harga</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Urutan</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($packages as $p)
                    <tr class="table-row">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                                     style="background:linear-gradient(135deg,#1B2337,#3B82F6);">
                                    {{ strtoupper(substr($p->name,0,1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $p->name }}</p>
                                    <p class="text-xs text-slate-400 max-w-sm truncate">{{ $p->description }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if($p->vehicle_type === 'roda_2')
                                <span class="badge badge-blue">Roda 2 (Motor)</span>
                            @elseif($p->vehicle_type === 'roda_4')
                                <span class="badge badge-purple">Roda 4 (Mobil)</span>
                            @else
                                <span class="badge badge-gray">Semua</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-slate-600 font-medium">
                            {{ $p->duration_minutes }} Menit
                        </td>
                        <td class="px-4 py-4 text-slate-800 font-bold">
                            Rp {{ number_format($p->price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4">
                            @if($p->is_active)
                                <span class="badge badge-green">Aktif</span>
                            @else
                                <span class="badge badge-red">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-slate-400 font-mono">
                            {{ $p->sort_order }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <a href="/packages/{{ $p->id }}/edit"
                                   class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button" @click="confirmDelete('{{ $p->id }}', '{{ addslashes($p->name) }}')" class="p-1.5 rounded-lg hover:bg-red-50 text-red-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                            Tidak ada paket layanan ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3.5 border-t border-slate-100">
            {{ $packages->links() }}
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div x-show="showAddModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4"
         @click.self="showAddModal=false">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Tambah Paket Layanan Baru</h3>
                <button @click="showAddModal=false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="/packages" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Paket *</label>
                        <input type="text" name="name" required placeholder="Premium Wax Wash"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Deskripsi Layanan</label>
                        <textarea name="description" rows="3" placeholder="Pencucian detail eksterior dan proteksi ban..."
                                  class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Tipe Kendaraan *</label>
                        <select name="vehicle_type" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                            <option value="roda_2">Roda 2 (Motor)</option>
                            <option value="roda_4" selected>Roda 4 (Mobil)</option>
                            <option value="all">Semua Tipe</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-650 mb-1.5 uppercase tracking-wide">Harga Paket (Rp) *</label>
                        <input type="number" name="price" required min="0" placeholder="75000"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Durasi (Menit) *</label>
                        <input type="number" name="duration_minutes" required min="1" placeholder="45" value="45"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-605 mb-1.5 uppercase tracking-wide">Sort Order</label>
                        <input type="number" name="sort_order" min="0" placeholder="1" value="0"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showAddModal=false"
                            class="px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-semibold rounded-xl text-slate-900 shadow"
                            style="background:#F0C419;">
                        Simpan Paket
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4"
         @click.self="showDeleteModal=false">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Hapus Paket Layanan</h3>
                <button @click="showDeleteModal=false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'/packages/'+deleteId" method="POST" class="p-6 space-y-4">
                @csrf @method('DELETE')
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-800">Apakah Anda yakin ingin menghapus paket layanan ini?</p>
                    <p class="text-xs text-slate-400">Paket dengan nama <span x-text="deleteName" class="font-bold text-slate-650"></span> akan dihapus secara permanen dari sistem.</p>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showDeleteModal=false" class="flex-1 px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-2.5 text-sm font-semibold rounded-xl text-white bg-red-500 hover:bg-red-600 shadow-sm">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function packagePage() {
    return {
        showAddModal: false,
        showDeleteModal: false,
        deleteId: '',
        deleteName: '',
        confirmDelete(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.showDeleteModal = true;
        }
    }
}
</script>
@endpush
