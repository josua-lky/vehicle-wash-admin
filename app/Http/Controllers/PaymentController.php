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
        return view('payments.index', compact('payments','stats'));
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
        $payment->update(['status'=>'refunded','refund_amount'=>$payment->amount,'refunded_at'=>now()]);
        $payment->booking?->update(['status'=>'cancelled','cancelled_reason'=>'Refund oleh admin']);
        return back()->with('success','Refund berhasil diproses.');
    }

    public function confirm(Payment $payment)
    {
        $payment->update(['status'=>'paid','paid_at'=>now()]);
        $payment->booking?->update(['status'=>'confirmed']);
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
            }
        }
        return response()->json(['status'=>'ok']);
    }

    public function export(Request $request)
    {
        return response()->json(['message'=>'Install maatwebsite/excel for export functionality.']);
    }
}
