<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id','payment_method','payment_provider','transaction_id',
        'status','amount','paid_at','expired_at','gateway_response',
        'refund_amount','refunded_at','refund_requested',
    ];
    protected $casts = [
        'amount'=>'decimal:2','refund_amount'=>'decimal:2',
        'paid_at'=>'datetime','expired_at'=>'datetime','refunded_at'=>'datetime',
        'gateway_response'=>'array',
        'refund_requested'=>'boolean',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }

    public function getMethodLabelAttribute()
    {
        return 'OnoPay';
    }
}
