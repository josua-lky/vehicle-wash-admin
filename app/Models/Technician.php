<?php
namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Technician extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

    protected $fillable = [
        'name','email','password','password_plain','phone','profile_photo','specialization',
        'area','outlet_id','status','rating','total_orders',
        'join_date','notes','fcm_token','latitude','longitude',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = ['rating'=>'float','join_date'=>'date','total_orders'=>'integer'];

    protected $appends = ['avatar'];

    public function outlet()   { return $this->belongsTo(Outlet::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function reviews()  { return $this->hasMany(Review::class); }

    public function getAvatarAttribute()
    {
        if (!$this->profile_photo) {
            return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=1B2337&color=F0C419';
        }
        if (str_starts_with($this->profile_photo, 'http://') || str_starts_with($this->profile_photo, 'https://')) {
            return $this->profile_photo;
        }
        return asset('storage/'.$this->profile_photo);
    }

    public function updateRating()
    {
        $avg = $this->reviews()->avg('rating');
        $this->update(['rating' => round($avg, 2), 'total_orders' => $this->bookings()->where('status','completed')->count()]);
    }
}
