<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Teknisi - {{ $data['invoice_code'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 40px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px double #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 16px 20px;
        }
        .meta-block h4 {
            margin: 0 0 6px 0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }
        .meta-block p {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
            padding: 10px 14px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        th:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }
        th:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            text-align: right;
        }
        td {
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        td:last-child {
            text-align: right;
            font-weight: 700;
        }
        .tech-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
        }
        .tech-contact {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }
        .breakdown-list {
            margin: 0;
            padding-left: 16px;
            font-size: 11px;
            color: #475569;
        }
        .breakdown-list li {
            margin-bottom: 4px;
        }
        .grand-total {
            background-color: #f8fafc;
            border-top: 2px solid #0f172a;
            font-size: 15px;
            font-weight: 800;
        }
        .grand-total td {
            padding: 16px 14px;
            color: #0f172a;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding: 0 20px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-box p {
            margin: 0;
        }
        .signature-line {
            margin-top: 70px;
            border-top: 1px solid #94a3b8;
            padding-top: 8px;
            font-weight: 700;
            color: #334155;
        }
        .no-print-area {
            margin-bottom: 30px;
            padding: 16px;
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .no-print-area p {
            margin: 0;
            font-size: 12px;
            color: #b45309;
            font-weight: 600;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-back:hover {
            background-color: #1e293b;
        }
        
        @media print {
            .no-print-area {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Alert & Action area to go back, hidden in print --}}
    <div class="no-print-area">
        <p>🖨️ Dialog pencetakan slip gaji telah dibuka otomatis. Silakan cetak atau simpan sebagai PDF.</p>
        <a href="{{ route('payments.index') }}" class="btn-back">Kembali ke Pembayaran</a>
    </div>

    {{-- Header --}}
    <div class="header">
        <div>
            <h1>Slip Pembayaran Gaji</h1>
            <p>Sistem Ekosistem Vehicle Wash</p>
        </div>
        <div style="text-align: right;">
            <p style="font-weight: 700; color: #0f172a; font-size: 14px;">VEHICLE WASH CO.</p>
            <p>support@vehiclewash.id</p>
        </div>
    </div>

    {{-- Meta Info --}}
    <div class="meta-info">
        <div class="meta-block">
            <h4>Nomor Invoice Gaji</h4>
            <p>{{ $data['invoice_code'] }}</p>
        </div>
        <div class="meta-block">
            <h4>Tanggal Dicetak</h4>
            <p>{{ $data['printed_at'] }}</p>
        </div>
        <div class="meta-block">
            <h4>Status Pembayaran</h4>
            <p style="color: #10b981;">LUNAS / PAID</p>
        </div>
    </div>

    {{-- Salary Details Table --}}
    <table>
        <thead>
            <tr>
                <th>Detail Teknisi</th>
                <th>Rincian Pekerjaan Selesai</th>
                <th>Detail Komisi & Gaji</th>
                <th style="text-align: right;">Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($data['payouts'] as $p)
            @php $grandTotal += $p['total_salary']; @endphp
            <tr>
                <td>
                    <div class="tech-name">{{ $p['name'] }}</div>
                    <div class="tech-contact">{{ $p['email'] }}</div>
                    <div class="tech-contact">{{ $p['phone'] }}</div>
                </td>
                <td>
                    <ul class="breakdown-list">
                        @if($p['home_orders'] > 0)
                        <li>Cuci di Rumah: <strong>{{ $p['home_orders'] }}</strong> booking (Total Omset: Rp {{ number_format($p['home_amount'], 0, ',', '.') }})</li>
                        @endif
                        @if($p['outlet_orders'] > 0)
                        <li>Cuci di Outlet: <strong>{{ $p['outlet_orders'] }}</strong> booking (Total Omset: Rp {{ number_format($p['outlet_amount'], 0, ',', '.') }})</li>
                        @endif
                    </ul>
                </td>
                <td>
                    <ul class="breakdown-list">
                        @if($p['home_orders'] > 0)
                        <li>Komisi Rumah (35%): <strong>Rp {{ number_format($p['home_amount'] * 0.35, 0, ',', '.') }}</strong></li>
                        @endif
                        @if($p['outlet_orders'] > 0)
                        <li>Komisi Outlet (25%): <strong>Rp {{ number_format($p['outlet_amount'] * 0.25, 0, ',', '.') }}</strong></li>
                        @endif
                    </ul>
                </td>
                <td>
                    Rp {{ number_format($p['total_salary'], 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            
            <tr class="grand-total">
                <td colspan="3" style="text-align: right; font-weight: 800; border: none;">TOTAL GAJI DIBAYARKAN:</td>
                <td style="border: none;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-box">
            <p>Dibuat oleh,</p>
            <div class="signature-line">Administrasi Keuangan</div>
        </div>
        <div class="signature-box">
            <p>Disetujui oleh,</p>
            <div class="signature-line">Manager Operational</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto open print dialog
            window.print();
        }
    </script>
</body>
</html>
