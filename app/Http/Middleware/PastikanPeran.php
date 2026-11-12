<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PastikanPeran
{
    public function handle(Request $request, Closure $next, string $peran): Response
    {
        $user = $request->user();
        $halamanLogin = $peran === 'admin' ? 'admin.login' : 'login';

        if (! $user) {
            return redirect()->route($halamanLogin);
        }

        // Akun dinonaktifkan admin → keluarkan
        if (! $user->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route($halamanLogin)
                ->withErrors(['username' => 'Akun Anda dinonaktifkan. Hubungi administrator.']);
        }

        if ($user->role !== $peran) {
            $rumah = $user->role === 'admin' ? 'admin.dashboard' : 'petugas.portal';

            return redirect()->route($rumah)->with('galat', 'Anda tidak memiliki akses ke halaman itu.');
        }

        return $next($request);
    }
}