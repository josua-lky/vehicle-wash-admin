<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $table = 'push_notifications';

    protected $fillable = [
        'customer_id', 'title', 'body', 'type', 'data', 'is_read', 'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public static function notifyAdmin(string $type, string $title, string $body, array $data = [])
    {
        $map = [
            'new_booking' => 'notify_new_booking',
            'payment_received' => 'notify_payment_received',
            'booking_cancelled' => 'notify_booking_cancelled',
            'bad_rating' => 'notify_bad_rating',
            'new_customer' => 'notify_new_customer',
        ];

        $settingKey = $map[$type] ?? null;
        if ($settingKey) {
            $isEnabled = \App\Models\Setting::get($settingKey, '1');
            if ($isEnabled !== '1') {
                return null;
            }
        }

        return self::create([
            'customer_id' => null,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
