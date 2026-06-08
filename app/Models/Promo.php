<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'name','code','type','value','min_transaction','max_usage',
        'max_usage_per_user','used_count','starts_at','expires_at','status','description',
    ];
    protected $casts = ['starts_at'=>'datetime','expires_at'=>'datetime','value'=>'decimal:2','min_transaction'=>'decimal:2','used_count'=>'integer'];

    public function usages()  { return $this->hasMany(PromoUsage::class); }
    public function bookings(){ return $this->hasMany(Booking::class); }

    public function getIsValidAttribute()
    {
        return $this->status==='active'
            && (!$this->starts_at || $this->starts_at <= now())
            && (!$this->expires_at || $this->expires_at >= now())
            && (!$this->max_usage || $this->used_count < $this->max_usage);
    }

    public function calculateDiscount(float $total): float
    {
        if ($this->min_transaction && $total < $this->min_transaction) return 0;
        return $this->type === 'percentage' ? min($total, $total * $this->value / 100) : $this->value;
    }
}
