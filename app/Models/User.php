<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','phone','profile_photo','is_active'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','is_active'=>'boolean'];

    const ROLES = [
        'super_admin'        => 'Super Admin',
        'admin_operasional'  => 'Admin Operasional',
        'admin_outlet'       => 'Admin Outlet',
        'admin_keuangan'     => 'Admin Keuangan',
    ];

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function canManageFinance(): bool { return in_array($this->role, ['super_admin','admin_keuangan']); }
    public function canManageOutlet(): bool  { return in_array($this->role, ['super_admin','admin_outlet','admin_operasional']); }

    public function activityLogs() { return $this->hasMany(ActivityLog::class); }

    public function vehicles()
    {
    return $this->hasMany(
        Vehicle::class,
        'customer_id'
        );
    }
}
