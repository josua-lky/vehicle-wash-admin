<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','email','phone','profile_photo','specialization',
        'area','outlet_id','status','rating','total_orders',
        'join_date','notes','fcm_token',
    ];

    protected $casts = ['rating'=>'float','join_date'=>'date','total_orders'=>'integer'];

    public function outlet()   { return $this->belongsTo(Outlet::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function reviews()  { return $this->hasMany(Review::class); }

    public function getAvatarAttribute()
    {
        return $this->profile_photo
            ? asset('storage/'.$this->profile_photo)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=1B2337&color=F0C419';
    }

    public function updateRating()
    {
        $avg = $this->reviews()->avg('rating');
        $this->update(['rating' => round($avg, 2), 'total_orders' => $this->bookings()->where('status','completed')->count()]);
    }
}
