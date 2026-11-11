<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasAuthController extends Controller
{
    public function form()
    {
        if (Auth::check()) {
            return redirect()->route('petugas.dashboard');
        }

        return view('petugas.login');
    }

    public function masuk(Request $request)
    {
        $kredensial = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Nama pengguna wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($kredensial, $request->boolean('ingat'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('petugas.dashboard'));
        }

        return back()
            ->withErrors(['username' => 'Nama pengguna atau kata sandi salah.'])
            ->onlyInput('username');
    }

    public function keluar(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}