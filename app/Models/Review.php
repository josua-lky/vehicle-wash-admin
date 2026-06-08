<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['booking_id','customer_id','technician_id','rating','comment'];
    protected $casts    = ['rating'=>'integer'];

    public function booking()    { return $this->belongsTo(Booking::class); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
}
