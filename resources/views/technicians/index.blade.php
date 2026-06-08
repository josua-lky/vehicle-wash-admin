@extends('layouts.app')
@section('title', 'Manajemen Teknisi')

@section('content')
<div class="p-6 space-y-5" x-data="techPage()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Technician Management</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage your service technicians and their workload.</p>
        </div>
        <button @click="showAddModal=true"
                class="flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg shadow"
                style="background:#F0C419; color:#1B2337;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Teknisi
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Total Teknisi</p>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['total'] ?? 89 }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['active_count'] ?? 24 }} aktif bertugas</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Pesanan Aktif</p>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['active_orders'] ?? 31 }}</p>
            <p class="text-xs text-slate-400 mt-1">sedang dikerjakan</p>
        </div>
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Rata-rata Bintang</p>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-3xl font-bold text-slate-800">{{ $stats['avg_rating'] ?? '4.82' }}</p>
                <div class="flex mt-1">
                    @for($i=1;$i<=5;$i++)
                    <svg class="w-4 h-4 {{ $i<=4?'':'opacity-30' }}" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN GRID: List + Detail --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Technician List --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            {{-- Search + Filters --}}
            <form method="GET" action="/technicians" class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3 w-full">
                <div class="relative flex-1 min-w-40">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teknisi..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
                </div>
                <select name="specialization" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-500 bg-slate-50">
                    <option value="">Semua Spesialisasi</option>
                    <option value="motor" {{ request('specialization') === 'motor' ? 'selected' : '' }}>Motor</option>
                    <option value="mobil" {{ request('specialization') === 'mobil' ? 'selected' : '' }}>Mobil</option>
                </select>
                <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-500 bg-slate-50">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="busy" {{ request('status') === 'busy' ? 'selected' : '' }}>Sibuk</option>
                </select>
                <select name="sort" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-500 bg-slate-50">
                    <option value="rating_desc" {{ request('sort') === 'rating_desc' ? 'selected' : '' }}>Rating Tertinggi</option>
                    <option value="rating_asc" {{ request('sort') === 'rating_asc' ? 'selected' : '' }}>Rating Terendah</option>
                </select>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#F8FAFC; border-bottom:2px solid #F1F5F9;">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Teknisi</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Spesialisasi</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Rating</th>
                            <th class="px-4 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                        $technicians = $technicians ?? collect([]);
                        $specColors = ['motor'=>'badge-blue','mobil'=>'badge-purple'];
                        $statusConfig = ['active'=>['Aktif','badge-green'],'inactive'=>['Nonaktif','badge-red'],'cuti'=>['Cuti','badge-yellow'],'busy'=>['Sibuk','badge-blue']];
                        @endphp
                        @foreach($technicians as $t)
                        @php
                            if (is_array($t)) {
                                $tId = $t['id'] ?? '';
                                $tName = $t['name'] ?? '-';
                                $tPhone = $t['phone'] ?? '-';
                                $tSpecialization = $t['specialization'] ?? 'motor';
                                $tStatus = $t['status'] ?? 'active';
                                $tRating = $t['rating'] ?? 0;
                                $tOrders = $t['orders'] ?? ($t['total_orders'] ?? 0);
                                $tArea = $t['area'] ?? '-';
                                $tSince = $t['since'] ?? ($t['join_date'] ?? '-');
                            } else {
                                $tId = $t->id;
                                $tName = $t->name;
                                $tPhone = $t->phone;
                                $tSpecialization = $t->specialization;
                                $tStatus = $t->status;
                                $tRating = $t->rating;
                                $tOrders = $t->total_orders;
                                $tArea = $t->area ?? '-';
                                $tSince = $t->join_date ? $t->join_date->format('M Y') : '-';
                            }
                            $tArray = [
                                'id' => $tId,
                                'name' => $tName,
                                'phone' => $tPhone,
                                'specialization' => $tSpecialization,
                                'status' => $tStatus,
                                'rating' => $tRating,
                                'orders' => $tOrders,
                                'area' => $tArea,
                                'since' => $tSince
                            ];
                        @endphp
                        <tr class="table-row cursor-pointer" @click="selectTech({{ json_encode($tArray) }})">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                                         style="background:linear-gradient(135deg,#1B2337,#3B82F6);">
                                        {{ strtoupper(substr($tName,0,1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-sm">{{ $tName }}</p>
                                        <p class="text-xs text-slate-400">{{ $tPhone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="badge {{ $specColors[strtolower($tSpecialization)] ?? 'badge-gray' }}">{{ ucfirst($tSpecialization) }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @php [$slabel,$sclass] = $statusConfig[$tStatus] ?? ['—','badge-gray']; @endphp
                                <span class="badge {{ $sclass }}">{{ $slabel }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    <span class="text-sm font-semibold text-slate-700">{{ number_format($tRating,1) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1" @click.stop>
                                    <a :href="'/technicians/'+{{ $tId }}+'/edit'"
                                       href="/technicians/{{ $tId }}/edit"
                                       class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="/technicians/{{ $tId }}" onsubmit="return confirm('Hapus teknisi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-red-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Menampilkan {{ count($technicians) }} teknisi</span>
                @if(isset($technicians) && method_exists($technicians, 'links'))
                    {{ $technicians->links() }}
                @else
                <div class="flex gap-1">
                    <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">← Prev</button>
                    <button class="px-3 py-1.5 rounded-lg text-white" style="background:#1B2337;">1</button>
                    <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">2</button>
                    <button class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50">Next →</button>
                </div>
                @endif
            </div>
        </div>

        {{-- RIGHT PANEL: Tech Detail Card --}}
        <div class="space-y-4">

            {{-- Detail Card --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <template x-if="!selected">
                    <div class="py-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <p class="text-sm">Klik baris teknisi untuk melihat detail</p>
                    </div>
                </template>
                <template x-if="selected">
                    <div>
                        {{-- Header --}}
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-bold text-white"
                                 style="background:linear-gradient(135deg,#1B2337,#3B82F6);">
                                <span x-text="selected.name.charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-800" x-text="selected.name"></h3>
                                <p class="text-xs text-slate-400" x-text="selected.phone"></p>
                                <span class="badge badge-blue mt-1" x-text="selected.specialization"></span>
                            </div>
                        </div>

                        {{-- Mini stats --}}
                        <div class="grid grid-cols-3 gap-2 mb-5">
                            <div class="text-center p-2.5 rounded-xl bg-slate-50">
                                <p class="text-lg font-bold text-slate-800" x-text="selected.orders"></p>
                                <p class="text-xs text-slate-400">Orders</p>
                            </div>
                            <div class="text-center p-2.5 rounded-xl bg-slate-50">
                                <p class="text-lg font-bold text-slate-800" x-text="selected.rating"></p>
                                <p class="text-xs text-slate-400">Rating</p>
                            </div>
                            <div class="text-center p-2.5 rounded-xl bg-slate-50">
                                <p class="text-lg font-bold text-slate-800">97%</p>
                                <p class="text-xs text-slate-400">On-time</p>
                            </div>
                        </div>

                        {{-- Info rows --}}
                        <div class="space-y-2.5 text-xs mb-4">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Area Kerja</span>
                                <span class="font-medium text-slate-700" x-text="selected.area"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Bergabung Sejak</span>
                                <span class="font-medium text-slate-700" x-text="selected.since"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Status</span>
                                <span class="badge badge-green" x-text="selected.status === 'active' ? 'Aktif' : 'Nonaktif'"></span>
                            </div>
                        </div>

                        {{-- Star rating visual --}}
                        <div class="mb-4">
                            <p class="text-xs text-slate-400 mb-2">Rata-rata Rating</p>
                            <div class="flex items-center gap-2">
                                <div class="flex">
                                    <template x-for="i in 5" :key="i">
                                        <svg class="w-4 h-4" :style="i<=Math.floor(selected.rating)?'color:#F0C419':'color:#E2E8F0'" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </template>
                                </div>
                                <span class="text-sm font-bold text-slate-700" x-text="selected.rating+' / 5.0'"></span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a :href="'/technicians/'+selected.id" class="flex-1 text-center py-2 rounded-xl text-xs font-semibold text-white" style="background:#1B2337;">
                                Lihat Profil
                            </a>
                            <a :href="'/technicians/'+selected.id+'/edit'" class="flex-1 text-center py-2 rounded-xl text-xs font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50">
                                Edit Data
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Premium Status --}}
            <div class="rounded-2xl p-5" style="background:linear-gradient(135deg,#1B2337,#252D41);">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" style="color:#F0C419;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <h3 class="text-white text-sm font-semibold">Premium Status</h3>
                </div>
                <p class="text-xs mb-3" style="color:#7C8DB5;">Teknisi performa terbaik mendapatkan bonus dan prioritas penugasan.</p>
                <div class="flex items-center justify-between text-xs">
                    <span style="color:#F0C419;" class="font-semibold">Top 3 Teknisi Bulan Ini</span>
                    <a href="/reports?tab=technicians" class="text-xs" style="color:#7C8DB5;">Lihat Semua →</a>
                </div>
            </div>

            {{-- Sertifikasi --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800 mb-3">Sertifikasi Teknik</h3>
                <div class="space-y-2">
                    @foreach(['Cuci Eksterior Premium','Detailing Interior','Engine Washing','Poles & Wax Profesional'] as $cert)
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $cert }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div x-show="showAddModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4"
         @click.self="showAddModal=false">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Tambah Teknisi Baru</h3>
                <button @click="showAddModal=false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="/technicians" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nama Lengkap *</label>
                        <input type="text" name="name" required placeholder="Ahmad Fauzi"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nomor HP *</label>
                        <input type="text" name="phone" required placeholder="0812-xxxx-xxxx"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" placeholder="teknisi@email.com"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Spesialisasi *</label>
                        <select name="specialization" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                            <option value="">Pilih spesialisasi</option>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Outlet</label>
                        <select name="outlet_id" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                            <option value="">Tanpa outlet (freelance)</option>
                            @foreach($outlets ?? [] as $outlet)
                            <option value="{{ $outlet['id'] }}">{{ $outlet['name'] }}</option>
                            @endforeach
                        </select>
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
                        Simpan Teknisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function techPage() {
    return {
        selected: null,
        showAddModal: false,
        selectTech(tech) { this.selected = tech; }
    }
}
</script>
@endpush
