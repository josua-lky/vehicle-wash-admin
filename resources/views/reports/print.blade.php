<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Kinerja Operasional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 p-8 antialiased">

    {{-- Print Controls / Header toolbar --}}
    <div class="no-print max-w-5xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-sm font-bold text-slate-700">Preview Cetak Laporan</h1>
            <p class="text-xs text-slate-400">Gunakan dialog cetak browser Anda untuk menyimpan berkas sebagai PDF.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-all">
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition-all">
                Tutup Halaman
            </button>
        </div>
    </div>

    {{-- Printable Report Area --}}
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm print:border-none print:shadow-none print:p-0">
        {{-- Report Header --}}
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Laporan Kinerja Operasional</h1>
                <p class="text-xs text-slate-500 mt-1">Ekosistem Manajemen Logistik & Layanan Vehicle Wash.</p>
            </div>
            <div class="text-right text-xs text-slate-400">
                <p class="font-bold text-slate-700">Dicetak Pada:</p>
                <p class="mt-0.5">{{ now()->isoFormat('D MMMM Y, H:i') }}</p>
                <p>Oleh: Admin Panel</p>
            </div>
        </div>

        {{-- Meta Info --}}
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 mb-6 grid grid-cols-2 gap-4 text-xs">
            <div>
                <p class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Periode Laporan</p>
                <p class="font-semibold text-slate-700 mt-1">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Outlet Peninjauan</p>
                <p class="font-semibold text-slate-700 mt-1">{{ $outletName }}</p>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="border border-slate-150 p-4 rounded-xl">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan</p>
                <p class="text-lg font-black text-emerald-600 mt-1">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
            </div>
            <div class="border border-slate-150 p-4 rounded-xl">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Pesanan Selesai</p>
                <p class="text-lg font-black text-slate-800 mt-1">{{ $ordersCount }}</p>
            </div>
            <div class="border border-slate-150 p-4 rounded-xl">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Rata-rata Order</p>
                <p class="text-lg font-black text-indigo-600 mt-1">Rp {{ number_format($avgAmount, 0, ',', '.') }}</p>
            </div>
            <div class="border border-slate-150 p-4 rounded-xl">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kepuasan Pelanggan</p>
                <p class="text-lg font-black text-amber-500 mt-1">{{ number_format($satisfaction, 1) }}%</p>
            </div>
        </div>

        {{-- Table Title --}}
        <h2 class="text-sm font-bold text-slate-800 mb-3 uppercase tracking-wider">Rincian Transaksi Baru</h2>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-[11px] text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Kode Booking</th>
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Layanan</th>
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Outlet</th>
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Total Biaya</th>
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Metode</th>
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="py-2.5 px-3 font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($bookings as $b)
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $b->booking_code }}</td>
                        <td class="py-2.5 px-3 text-slate-600">{{ $b->customer->name ?? '—' }}</td>
                        <td class="py-2.5 px-3 text-slate-600">{{ $b->package->name ?? $b->vehicle_name ?? '—' }}</td>
                        <td class="py-2.5 px-3 text-slate-500">{{ $b->outlet->name ?? '—' }}</td>
                        <td class="py-2.5 px-3 font-bold text-slate-800">Rp {{ number_format($b->total_amount, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-slate-600 capitalize">{{ $b->payment->payment_method ?? '—' }}</td>
                        <td class="py-2.5 px-3">
                            <span class="font-bold uppercase tracking-wider text-[8px] px-1.5 py-0.5 rounded
                                {{ $b->status === 'completed' ? 'text-emerald-700 bg-emerald-50' : ($b->status === 'cancelled' ? 'text-rose-700 bg-rose-50' : 'text-amber-700 bg-amber-50') }}">
                                {{ $b->status }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-slate-500">{{ $b->completed_at ? $b->completed_at->format('d M Y, H:i') : $b->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @endforeach
                    @if($bookings->isEmpty())
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada transaksi terdaftar pada periode ini.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Footer/Signature --}}
        <div class="mt-12 flex justify-between items-end border-t border-slate-100 pt-6">
            <div class="text-[10px] text-slate-400 leading-normal">
                <p>Dokumen Laporan Kinerja ini dihasilkan secara otomatis oleh sistem logistik Vehicle Wash.</p>
                <p>Seluruh angka keuangan dan statistik order sinkron dengan data operasional outlet.</p>
            </div>
            <div class="text-right text-xs pr-4">
                <p class="text-slate-400 mb-10">Tanda Tangan Penanggung Jawab,</p>
                <p class="font-bold text-slate-800">________________________</p>
                <p class="text-[10px] text-slate-400 mt-1">Direktur Operasional</p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
