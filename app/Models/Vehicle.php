<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['customer_id','type','brand','model','color','license_plate','year','notes'];
    public function customer() { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function bookings() { return $this->hasMany(Booking::class); }
}
