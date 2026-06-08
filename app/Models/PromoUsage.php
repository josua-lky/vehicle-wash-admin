<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoUsage extends Model
{
    protected $fillable = ['promo_id','customer_id','booking_id','discount_applied'];
    protected $casts    = ['discount_applied'=>'decimal:2'];

    public function promo()    { return $this->belongsTo(Promo::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function booking()  { return $this->belongsTo(Booking::class); }
}
