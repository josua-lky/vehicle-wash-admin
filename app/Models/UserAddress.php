<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = ['customer_id','label','address','latitude','longitude','is_default'];
    protected $casts    = ['is_default'=>'boolean','latitude'=>'decimal:8','longitude'=>'decimal:8'];

    public function customer() { return $this->belongsTo(Customer::class); }
}
