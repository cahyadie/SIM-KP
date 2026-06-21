<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Menerima ...$roles (titik tiga) agar bisa menampung banyak role
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek Login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Cek Role (Apakah role user ada di dalam daftar role yang diizinkan?)
        // Contoh: Jika user 'kaprodi', dan yang diizinkan ['admin', 'kaprodi'], maka BOLEH.
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 3. Jika tidak ada yang cocok, Tolak.
        abort(403, 'AKSES DITOLAK! Role Anda (' . $user->role . ') tidak memiliki izin.');
    }
}