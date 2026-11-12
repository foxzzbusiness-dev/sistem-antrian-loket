<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Layanan;
use App\Models\Loket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /** Petugas dianggap online bila portalnya mengirim sinyal dalam 45 detik terakhir. */
    private const BATAS_ONLINE_DETIK = 45;

    // ------------------------------------------------------------------
    // Ringkasan
    // ------------------------------------------------------------------
    public function dashboard()
    {
        $hariIni = fn () => Antrean::whereDate('diterbitkan_at', today());

        $petugas = User::where('role', 'petugas')->where('aktif', true)->orderBy('loket')->get()->map(function (User $u) {
            $aktif = $u->loket
                ? Antrean::with('layanan')->where('status', 'dilayani')->where('loket', $u->loket)->first()
                : null;

            $selesai = $u->loket
                ? Antrean::whereDate('selesai_at', today())->where('status', 'selesai')->where('loket', $u->loket)->get()
                : collect();

            $rata = $selesai->filter(fn ($a) => $a->dimulai_at && $a->selesai_at)
                ->avg(fn ($a) => $a->dimulai_at->diffInMinutes($a->selesai_at));

            $terakhir = $u->terakhir_aktif_at ? Carbon::parse($u->terakhir_aktif_at) : null;
            $online = $terakhir && $terakhir->gt(now()->subSeconds(self::BATAS_ONLINE_DETIK));

            return (object) [
                'nama' => $u->name,
                'loket' => $u->loket ?? '—',
                'status' => ! $online ? 'offline' : ($aktif ? 'melayani' : 'siaga'),
                'nomor' => $aktif?->nomor,
                'layanan' => $aktif?->layanan?->nama,
                'durasi' => $aktif?->dimulai_at ? (int) $aktif->dimulai_at->diffInMinutes(now()) : null,
                'selesai' => $selesai->count(),
                'rata' => $rata !== null ? (int) round($rata) : null,
                'terakhir' => $terakhir,
            ];
        });

        $terlama = $hariIni()->where('status', 'menunggu')->orderBy('diterbitkan_at')->first();

        $tiket = $hariIni()->with('layanan')
            ->orderByRaw("case status when 'dilayani' then 0 when 'menunggu' then 1 when 'selesai' then 2 else 3 end")
            ->orderBy('diterbitkan_at')->orderBy('id')
            ->paginate(8)->withQueryString();

        return view('admin.dashboard', [
            'petugas' => $petugas,
            'tiket' => $tiket,
            'stat' => [
                'online' => $petugas->where('status', '!=', 'offline')->count(),
                'total_petugas' => $petugas->count(),
                'melayani' => $petugas->where('status', 'melayani')->count(),
                'menunggu' => $hariIni()->where('status', 'menunggu')->count(),
                'selesai' => $hariIni()->where('status', 'selesai')->count(),
                'terlama' => $terlama ? (int) $terlama->diterbitkan_at->diffInMinutes(now()) : null,
            ],
            'perLayanan' => Layanan::withCount(['antrean as menunggu_count' => fn ($q) => $q
                ->whereDate('diterbitkan_at', today())->where('status', 'menunggu')])
                ->orderBy('kode')->get(),
        ]);
    }

    /** Admin membatalkan tiket yang menunggu atau sedang dilayani. */
    public function batalTiket(Antrean $antrean)
    {
        if (! in_array($antrean->status, ['menunggu', 'dilayani'])) {
            return back()->with('galat', 'Tiket ini sudah selesai atau dibatalkan.');
        }

        $antrean->update(['status' => 'batal', 'selesai_at' => now()]);

        return back()->with('sukses', "Tiket {$antrean->nomor} dibatalkan.");
    }

    // ------------------------------------------------------------------
    // Kelola petugas
    // ------------------------------------------------------------------
    public function petugas(Request $request)
    {
        $dasar = fn () => User::where('role', 'petugas');

        $stat = [
            'total' => $dasar()->count(),
            'aktif' => $dasar()->where('aktif', true)->count(),
            'bertugas' => $dasar()->where('aktif', true)
                ->where('terakhir_aktif_at', '>', now()->subSeconds(self::BATAS_ONLINE_DETIK))->count(),
            'nonaktif' => $dasar()->where('aktif', false)->count(),
        ];

        $daftar = $dasar()
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$v}%")
                ->orWhere('kode', 'like', "%{$v}%")
                ->orWhere('email', 'like', "%{$v}%")
                ->orWhere('username', 'like', "%{$v}%")))
            ->orderBy('kode')->paginate(10)->withQueryString();

        return view('admin.petugas', [
            'stat' => $stat,
            'daftar' => $daftar,
            'daftarLoket' => Loket::orderBy('nama')->pluck('nama'),
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());

        User::forceCreate([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'petugas',
            'kode' => $this->kodeBerikut(),
            'loket' => $data['loket'] ?? null,
            'shift_mulai' => $data['shift_mulai'],
            'shift_selesai' => $data['shift_selesai'],
            'aktif' => true,
        ]);

        return redirect()->route('admin.petugas')->with('sukses', "Petugas {$data['name']} ditambahkan.");
    }

    public function ubah(Request $request, User $user)
    {
        abort_unless($user->role === 'petugas', 404);

        $data = $request->validate($this->aturan($user), $this->pesan());

        if ($user->loket !== ($data['loket'] ?? null) && $this->sedangMelayani($user)) {
            return back()->withInput()->with('galat', 'Petugas sedang melayani antrean. Selesaikan dulu sebelum memindahkan loket.');
        }

        $user->forceFill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'loket' => $data['loket'] ?? null,
            'shift_mulai' => $data['shift_mulai'],
            'shift_selesai' => $data['shift_selesai'],
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.petugas')->with('sukses', "Data {$user->name} diperbarui.");
    }

    public function status(User $user)
    {
        abort_unless($user->role === 'petugas', 404);

        if ($user->aktif && $this->sedangMelayani($user)) {
            return back()->with('galat', "{$user->name} sedang melayani antrean. Tunggu sampai selesai sebelum menonaktifkan.");
        }

        $user->forceFill(['aktif' => ! $user->aktif, 'terakhir_aktif_at' => null])->save();

        return back()->with('sukses', $user->name.($user->aktif ? ' diaktifkan.' : ' dinonaktifkan.'));
    }

    public function hapus(User $user)
    {
        abort_unless($user->role === 'petugas', 404);

        if ($this->sedangMelayani($user)) {
            return back()->with('galat', "{$user->name} sedang melayani antrean. Tidak bisa dihapus sekarang.");
        }

        $user->delete();

        return back()->with('sukses', "Akun {$user->name} dihapus.");
    }

    // ------------------------------------------------------------------
    private function sedangMelayani(User $u): bool
    {
        return $u->loket
            && Antrean::where('status', 'dilayani')->where('loket', $u->loket)->exists();
    }

    private function kodeBerikut(): string
    {
        $maks = User::where('role', 'petugas')->pluck('kode')
            ->map(fn ($k) => (int) substr((string) $k, 4))->max() ?? 0;

        return sprintf('PTO-%03d', $maks + 1);
    }

    private function aturan(?User $u = null): array
    {
        return [
            'name' => 'required|string|max:100',
            'username' => ['required', 'alpha_dash', 'max:50', Rule::unique('users', 'username')->ignore($u)],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($u)],
            'loket' => ['nullable', Rule::exists('loket', 'nama'), Rule::unique('users', 'loket')->ignore($u)],
            'shift_mulai' => 'required|date_format:H:i',
            'shift_selesai' => 'required|date_format:H:i|after:shift_mulai',
            'password' => [$u ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    private function pesan(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Nama pengguna wajib diisi.',
            'username.unique' => 'Nama pengguna sudah dipakai.',
            'username.alpha_dash' => 'Nama pengguna hanya boleh huruf, angka, strip, dan garis bawah.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai.',
            'loket.unique' => 'Loket ini sudah ditempati petugas lain.',
            'loket.exists' => 'Loket tidak ditemukan.',
            'shift_mulai.required' => 'Jam mulai shift wajib diisi.',
            'shift_selesai.required' => 'Jam selesai shift wajib diisi.',
            'shift_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ];
    }
}