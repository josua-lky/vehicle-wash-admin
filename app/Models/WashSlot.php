<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WashSlot extends Model
{
    protected $fillable = ['outlet_id','slot_date','slot_time','capacity','booked_count','status'];
    protected $casts    = ['slot_date'=>'date','booked_count'=>'integer','capacity'=>'integer'];

    public function outlet()   { return $this->belongsTo(Outlet::class); }
    public function bookings() { return $this->hasMany(Booking::class, 'outlet_slot_id'); }

    public function getIsFullAttribute()    { return $this->booked_count >= $this->capacity; }
    public function getAvailableAttribute() { return max(0, $this->capacity - $this->booked_count); }
}
