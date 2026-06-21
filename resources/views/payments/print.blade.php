<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Pembayaran</title>
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
            <h1 class="text-sm font-bold text-slate-700">Preview Cetak PDF</h1>
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
                <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Laporan Transaksi Pembayaran</h1>
                <p class="text-xs text-slate-500 mt-1">Sistem Manajemen Ekosistem Vehicle Wash logistics.</p>
            </div>
            <div class="text-right text-xs text-slate-400">
                <p class="font-bold text-slate-700">Dicetak Pada:</p>
                <p class="mt-0.5">{{ now()->isoFormat('D MMMM Y, H:i') }}</p>
                <p>Oleh: Admin Panel</p>
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</p>
                <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $payments->count() }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Nominal (Paid)</p>
                <p class="text-xl font-bold text-emerald-600 mt-0.5">Rp {{ number_format($payments->where('status','paid')->sum('amount'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nominal Pending</p>
                <p class="text-xl font-bold text-amber-600 mt-0.5">Rp {{ number_format($payments->where('status','pending')->sum('amount'), 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 px-4 font-bold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 font-bold text-slate-500 uppercase tracking-wider">Kode Booking</th>
                        <th class="py-3 px-4 font-bold text-slate-500 uppercase tracking-wider">Nama Pelanggan</th>
                        <th class="py-3 px-4 font-bold text-slate-500 uppercase tracking-wider">Jumlah (IDR)</th>
                        <th class="py-3 px-4 font-bold text-slate-500 uppercase tracking-wider">Metode</th>
                        <th class="py-3 px-4 font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($payments as $p)
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3 px-4 font-semibold text-slate-800">#{{ $p->id }}</td>
                        <td class="py-3 px-4 font-medium text-slate-600">{{ $p->booking?->booking_code ?? '—' }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $p->booking?->customer?->name ?? '—' }}</td>
                        <td class="py-3 px-4 font-bold text-slate-800">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-slate-600 capitalize">{{ $p->payment_method ?? '—' }}</td>
                        <td class="py-3 px-4">
                            <span class="font-bold uppercase tracking-wider text-[9px] px-2 py-0.5 rounded-full
                                {{ $p->status === 'paid' ? 'text-emerald-700 bg-emerald-50' : ($p->status === 'pending' ? 'text-amber-700 bg-amber-50' : 'text-rose-700 bg-rose-50') }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500">{{ $p->created_at ? $p->created_at->format('d M Y, H:i') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer/Signature --}}
        <div class="mt-12 flex justify-between items-end border-t border-slate-100 pt-6">
            <div class="text-[10px] text-slate-400 leading-normal">
                <p>Dokumen ini dihasilkan secara otomatis oleh sistem logistik Vehicle Wash.</p>
                <p>Semua transaksi bersifat final dan terdaftar di server database.</p>
            </div>
            <div class="text-right text-xs pr-4">
                <p class="text-slate-400 mb-10">Tanda Tangan Penanggung Jawab,</p>
                <p class="font-bold text-slate-800">________________________</p>
                <p class="text-[10px] text-slate-400 mt-1">Super Administrator</p>
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
