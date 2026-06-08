<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return redirect('/login')->with('error', 'Akun Anda telah dinonaktifkan.');
        }
        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        return $next($request);
    }
}
