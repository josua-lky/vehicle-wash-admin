<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id','action','model_type','model_id','old_values','new_values','ip_address','user_agent'];
    protected $casts    = ['old_values'=>'array','new_values'=>'array'];
    const UPDATED_AT    = null;
    const CREATED_AT    = 'created_at';

    public function user() { return $this->belongsTo(User::class); }

    public static function log(string $action, $model = null, array $old = [], array $new = []): void
    {
        static::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->id,
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
