<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;
    
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($booking) {
            if (!empty($booking->technician_id)) {
                $tech = Technician::find($booking->technician_id);
                if ($tech) {
                    $booking->outlet_id = $tech->outlet_id;
                }
            }
        });
    }

    protected $fillable = [
        'booking_code','customer_id','vehicle_id','vehicle_name','vehicle_type',
        'service_type','outlet_id','outlet_slot_id','technician_id',
        'service_address','latitude','longitude',
        'scheduled_at','status','promo_id',
        'subtotal','discount_amount','total_amount',
        'package_id','notes','cancelled_reason','completed_at',
        'before_photo', 'after_photo', 'salary_paid',
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'completed_at'  => 'datetime',
        'subtotal'      => 'decimal:2',
        'discount_amount'=>'decimal:2',
        'total_amount'  => 'decimal:2',
        'latitude'      => 'decimal:8',
        'longitude'     => 'decimal:8',
        'salary_paid'   => 'boolean',
    ];

    public function customer()   { return $this->belongsTo(Customer::class); }
    public function vehicle()    { return $this->belongsTo(Vehicle::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
    public function package()    { return $this->belongsTo(Package::class); }
    public function outlet()     { return $this->belongsTo(Outlet::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function review()     { return $this->hasOne(Review::class); }
    public function promo()      { return $this->belongsTo(Promo::class); }

    public function scopePending($q)    { return $q->where('status','pending'); }
    public function scopeActive($q)     { return $q->whereIn('status',['confirmed','assigned','on_way','in_progress']); }
    public function scopeCompleted($q)  { return $q->where('status','completed'); }

    public function getStatusLabelAttribute()
    {
        $map = ['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi','assigned'=>'Ditugaskan',
                'on_way'=>'Dalam Perjalanan','in_progress'=>'Dikerjakan','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
        return $map[$this->status] ?? $this->status;
    }
}
