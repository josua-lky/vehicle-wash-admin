<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'profile_photo',
        'status',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['avatar'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function vehicles()
    {
        return $this->hasMany(
            Vehicle::class,
            'customer_id'
        );
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAvatarAttribute()
    {
        if (!$this->profile_photo) {
            return 'https://ui-avatars.com/api/?name='
                .urlencode($this->name)
                .'&background=1B2337&color=F0C419';
        }
        if (str_starts_with($this->profile_photo, 'http://') || str_starts_with($this->profile_photo, 'https://')) {
            return $this->profile_photo;
        }
        return asset('storage/'.$this->profile_photo);
    }
}