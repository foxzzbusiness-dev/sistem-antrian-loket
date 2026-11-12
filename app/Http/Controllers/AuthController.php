<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private const HALAMAN = [
        'petugas' => [
            'judul' => 'Login Petugas • Sistem Antrian Loket',
            'badge' => 'PORTAL PETUGAS',
            'deskripsi' => 'Masuk ke akun petugas untuk memanggil dan melayani antrean di loket Anda.',
            'aksi' => 'petugas.masuk',
        ],
        'admin' => [
            'judul' => 'Login Admin • Sistem Antrian Loket',
            'badge' => 'ADMINISTRATOR',
            'deskripsi' => 'Masuk ke akun administrator untuk memantau petugas dan aktivitas pelayanan.',
            'aksi' => 'admin.masuk',
        ],
    ];

    public function formPetugas() { return $this->form('petugas'); }
    public function formAdmin()   { return $this->form('admin'); }
    public function masukPetugas(Request $r) { return $this->proses($r, 'petugas'); }
    public function masukAdmin(Request $r)   { return $this->proses($r, 'admin'); }

    public function keluar(Request $request)
    {
        $user = $request->user();
        $peran = $user->role;

        // Tandai offline supaya langsung terlihat di dashboard admin
        User::whereKey($user->id)->update(['terakhir_aktif_at' => null]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($peran === 'admin' ? 'admin.login' : 'login');
    }

    private function tujuan(User $user): string
    {
        return $user->role === 'admin' ? route('admin.dashboard') : route('petugas.portal');
    }

    private function form(string $peran)
    {
        if (Auth::check()) {
            return redirect($this->tujuan(Auth::user()));
        }

        $h = self::HALAMAN[$peran];

        return view('petugas.login', [
            'judul' => $h['judul'],
            'badge' => $h['badge'],
            'deskripsi' => $h['deskripsi'],
            'aksi' => route($h['aksi']),
        ]);
    }
        private function proses(Request $request, string $peran)
{
    $data = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ], [
        'username.required' => 'Nama pengguna wajib diisi.',
        'password.required' => 'Kata sandi wajib diisi.',
    ]);

    if (Auth::attempt($data + ['role' => $peran, 'aktif' => true], $request->boolean('ingat'))) {
        $request->session()->regenerate();

        return redirect($this->tujuan(Auth::user()));
    }

    // Beri petunjuk yang tepat bila kata sandi benar tapi akun tidak bisa dipakai di sini
    $ada = User::where('username', $data['username'])->first();

    if ($ada && Hash::check($data['password'], $ada->password)) {
        $pesan = ! $ada->aktif
            ? 'Akun Anda dinonaktifkan. Hubungi administrator.'
            : 'Akun ini bukan akun '.$peran.'. Gunakan halaman login '.($peran === 'admin' ? 'petugas' : 'admin').'.';
    } else {
        $pesan = 'Nama pengguna atau kata sandi salah.';
    }

    return back()->withErrors(['username' => $pesan])->onlyInput('username');
}
}