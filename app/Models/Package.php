<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name','description','vehicle_type','price','duration_minutes','is_active','sort_order'];
    protected $casts    = ['price'=>'decimal:2','is_active'=>'boolean','duration_minutes'=>'integer'];

    public function bookings() { return $this->hasMany(Booking::class); }
    public function outlets()  { return $this->belongsToMany(Outlet::class,'outlet_packages'); }

    public function scopeActive($q) { return $q->where('is_active',true); }
    public function scopeForVehicle($q,$type) { return $q->where('vehicle_type',$type); }
}
