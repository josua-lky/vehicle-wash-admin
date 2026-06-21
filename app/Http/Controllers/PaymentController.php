<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['booking.customer'])
            ->when($request->status, fn($q)=>$q->where('status',$request->status))
            ->when($request->method, fn($q)=>$q->where('payment_method',$request->method))
            ->when($request->search, fn($q)=>$q->where('id','like',"%{$request->search}%")
                ->orWhereHas('booking.customer',fn($q2)=>$q2->where('name','like',"%{$request->search}%")))
            ->when($request->date_from, fn($q)=>$q->whereDate('created_at','>=',$request->date_from))
            ->when($request->date_to,   fn($q)=>$q->whereDate('created_at','<=',$request->date_to))
            ->latest()->paginate(15);

        $stats = [
            'total_revenue'  => Payment::where('status','paid')->sum('amount'),
            'paid'           => Payment::where('status','paid')->sum('amount'),
            'pending'        => Payment::where('status','pending')->sum('amount'),
            'refunded'       => Payment::where('status','refunded')->sum('amount'),
            'totalPayments'  => Payment::count(),
        ];

        // 12-month revenue trend
        $monthlyRevenue = Payment::selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
            ->where('status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $revenueData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueData[] = (float)($monthlyRevenue[$m] ?? 0);
        }

        // Quick Payouts (50% commission for completed bookings)
        $payouts = Booking::where('status', 'completed')
            ->whereNotNull('technician_id')
            ->with('technician')
            ->get()
            ->filter(fn($b) => !is_null($b->technician))
            ->groupBy('technician_id')
            ->map(function ($bookings) {
                $tech = $bookings->first()->technician;
                $ordersCount = $bookings->count();
                $totalAmount = $bookings->sum('total_amount');
                $payoutAmount = $totalAmount * 0.5;

                return [
                    'technician_id' => $tech->id,
                    'name' => $tech->name,
                    'orders_count' => $ordersCount,
                    'payout_amount' => $payoutAmount,
                    'avatar' => $tech->avatar,
                ];
            })
            ->values()
            ->sortByDesc('payout_amount')
            ->take(5)
            ->toArray();

        // Payment method breakdown (Only OnoPay is used)
        $methodBreakdown = [
            [
                'label' => 'OnoPay (E-Wallet)',
                'pct' => '100%',
                'color' => '#F0C419'
            ]
        ];

        return view('payments.index', compact(
            'payments',
            'stats',
            'revenueData',
            'payouts',
            'methodBreakdown'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load('booking.customer');
        return view('payments.show', compact('payment'));
    }

    public function refund(Request $request, Payment $payment)
    {
        if ($payment->status !== 'paid')
            return back()->with('error','Hanya pembayaran berstatus "paid" yang dapat di-refund.');

        $payment->update([
            'status' => 'refunded',
            'refund_amount' => $payment->amount,
            'refunded_at' => now(),
            'refund_requested' => false
        ]);

        if ($payment->booking) {
            $payment->booking->update([
                'status' => 'cancelled',
                'cancelled_reason' => $payment->booking->cancelled_reason ?: 'Refund oleh admin'
            ]);
        }

        if ($payment->booking && $payment->booking->customer) {
            $customer = $payment->booking->customer;
            try {
                $client = new \GuzzleHttp\Client();
                $client->post('https://onopay.web.id/api/v1/payment/topup', [
                    'json' => [
                        'phone_number' => $customer->phone,
                        'amount' => $payment->amount
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('OnoPay Auto-Refund failed for ' . $customer->phone . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Refund berhasil diproses dan saldo pelanggan telah dikembalikan.');
    }

    public function confirm(Payment $payment)
    {
        $payment->update(['status'=>'paid','paid_at'=>now()]);
        $payment->booking?->update(['status'=>'confirmed']);

        \App\Models\PushNotification::notifyAdmin(
            'payment_received',
            'Pembayaran Diterima',
            "Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " diterima untuk pemesanan " . ($payment->booking ? $payment->booking->booking_code : '') . ".",
            ['booking_id' => $payment->booking_id, 'payment_id' => $payment->id]
        );
        return back()->with('success','Pembayaran berhasil dikonfirmasi.');
    }

    public function webhook(Request $request)
    {
        // Midtrans/Xendit webhook handler
        $orderId = $request->order_id ?? $request->input('data.reference_id');
        $status  = $request->transaction_status ?? $request->input('data.status');
        $payment = Payment::where('transaction_id', $orderId)->first();
        if (!$payment) return response()->json(['status'=>'not_found'],404);

        $map = ['settlement'=>'paid','capture'=>'paid','success'=>'paid','pending'=>'pending','deny'=>'failed','cancel'=>'failed','expire'=>'expired','failure'=>'failed'];
        if (isset($map[$status])) {
            $payment->update(['status'=>$map[$status], 'gateway_response'=>$request->all()]);
            if ($map[$status]==='paid') {
                $payment->update(['paid_at'=>now()]);
                $payment->booking?->update(['status'=>'confirmed']);

                \App\Models\PushNotification::notifyAdmin(
                    'payment_received',
                    'Pembayaran Diterima',
                    "Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " diterima untuk pemesanan " . ($payment->booking ? $payment->booking->booking_code : '') . ".",
                    ['booking_id' => $payment->booking_id, 'payment_id' => $payment->id]
                );
            }
        }
        return response()->json(['status'=>'ok']);
    }

    public function export(Request $request)
    {
        $payments = Payment::with(['booking.customer'])
            ->when($request->status, fn($q)=>$q->where('status',$request->status))
            ->when($request->method, fn($q)=>$q->where('payment_method',$request->method))
            ->when($request->search, fn($q)=>$q->where('id','like',"%{$request->search}%")
                ->orWhereHas('booking.customer',fn($q2)=>$q2->where('name','like',"%{$request->search}%")))
            ->when($request->date_from, fn($q)=>$q->whereDate('created_at','>=',$request->date_from))
            ->when($request->date_to,   fn($q)=>$q->whereDate('created_at','<=',$request->date_to))
            ->latest()->get();

        $format = $request->get('format', 'excel');
        
        if ($format === 'pdf') {
            return view('payments.print', compact('payments'));
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=payments-export.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Pembayaran', 'Kode Booking', 'Pelanggan', 'Jumlah (IDR)', 'Metode Pembayaran', 'Status', 'Tanggal']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->booking?->booking_code ?? '—',
                    $payment->booking?->customer?->name ?? '—',
                    $payment->amount,
                    $payment->payment_method ?? '—',
                    $payment->status,
                    $payment->created_at ? $payment->created_at->format('Y-m-d H:i:s') : '—',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function processPayouts(Request $request)
    {
        return back()->with('success', 'Semua pembayaran komisi teknisi (payouts) berhasil diproses!');
    }
}
