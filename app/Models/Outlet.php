<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','address','phone','latitude','longitude',
        'capacity_per_hour','open_time','close_time','status','photo',
    ];

    protected $casts = ['latitude'=>'decimal:8','longitude'=>'decimal:8'];

    public function technicians() { return $this->hasMany(Technician::class); }
    public function slots()       { return $this->hasMany(WashSlot::class); }
    public function bookings()    { return $this->hasMany(Booking::class); }
    public function packages()    { return $this->belongsToMany(Package::class, 'outlet_packages'); }
    public function reviews()     { return $this->hasMany(Review::class); }

    public function updateRating()
    {
        $avg = $this->reviews()->whereNotNull('outlet_rating')->avg('outlet_rating');
        $this->update(['rating' => $avg ? round($avg, 2) : 0.00]);
    }

    public function getAvailableSlotsForDate(string $date)
    {
        return $this->slots()->whereDate('slot_date',$date)
                    ->where('status','available')
                    ->whereRaw('booked_count < capacity')
                    ->orderBy('slot_time')->get();
    }
}
