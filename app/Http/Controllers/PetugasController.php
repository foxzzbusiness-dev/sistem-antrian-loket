<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Layanan;
use App\Models\Loket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    private function hariIni()
    {
        return Antrean::whereDate('diterbitkan_at', today());
    }

    private function aktifDi(?string $loket): ?Antrean
    {
        return $loket
            ? Antrean::with('layanan')->where('status', 'dilayani')->where('loket', $loket)->first()
            : null;
    }

    public function dashboard(Request $request)
    {
        $petugas = $request->user();

        $stat = [
            'total' => $this->hariIni()->count(),
            'menunggu' => $this->hariIni()->where('status', 'menunggu')->count(),
            'dilayani' => $this->hariIni()->where('status', 'dilayani')->count(),
            'selesai' => $this->hariIni()->where('status', 'selesai')->count(),
        ];

        $aktif = $this->aktifDi($petugas->loket);
        $berikutnya = $this->hariIni()->where('status', 'menunggu')
            ->orderBy('diterbitkan_at')->orderBy('id')->first();

        $daftar = $this->hariIni()->with('layanan')
            ->when($request->q, fn ($q, $v) => $q->where('nomor', 'like', "%{$v}%"))
            ->when($request->layanan, fn ($q, $v) => $q->where('layanan_id', $v))
            ->orderByRaw("case status when 'dilayani' then 0 when 'menunggu' then 1 when 'selesai' then 2 else 3 end")
            ->orderBy('diterbitkan_at')->orderBy('id')
            ->paginate(5)->withQueryString();

        return view('petugas.dashboard', [
            'petugas' => $petugas,
            'stat' => $stat,
            'aktif' => $aktif,
            'berikutnya' => $berikutnya,
            'daftar' => $daftar,
            'daftarLayanan' => Layanan::orderBy('kode')->get(),
            'daftarLoket' => Loket::orderBy('nama')->get(),
            'daftarPengguna' => User::orderBy('id')->take(2)->get(),
        ]);
    }

    public function panggil(Request $request)
    {
        $petugas = $request->user();
     
        
        if (! $petugas->loket) {
            return back()->with('galat', 'Akun Anda belum ditempatkan di loket.');
        }

        $hasil = DB::transaction(function () use ($petugas) {
            if ($this->aktifDi($petugas->loket)) {
                return 'aktif';
            }

            $next = $this->hariIni()->where('status', 'menunggu')
                ->orderBy('diterbitkan_at')->orderBy('id')->lockForUpdate()->first();

            if (! $next) {
                return 'kosong';
            }

            $next->update([
                'status' => 'dilayani',
                'loket' => $petugas->loket,
                'dimulai_at' => now(),
                'dipanggil_at' => now(),
            ]);

            return $next;
        });

        return match (true) {
            $hasil === 'aktif' => back()->with('galat', 'Selesaikan layanan yang sedang berjalan dulu.'),
            $hasil === 'kosong' => back()->with('galat', 'Tidak ada antrean yang menunggu.'),
            default => back()->with('sukses', "Memanggil {$hasil->nomor} ke {$petugas->loket}."),
        };
    }

    public function ulang(Request $request)
    {
        $aktif = $this->aktifDi($request->user()->loket);

        if (! $aktif) {
            return back()->with('galat', 'Belum ada antrean yang sedang dilayani.');
        }

        $aktif->update(['dipanggil_at' => now()]);

        return back()->with('sukses', "Memanggil ulang {$aktif->nomor} ke {$aktif->loket}.");
    }

    public function selesai(Request $request)
    {
        $aktif = $this->aktifDi($request->user()->loket);

        if (! $aktif) {
            return back()->with('galat', 'Belum ada antrean yang sedang dilayani.');
        }

        $aktif->update(['status' => 'selesai', 'selesai_at' => now()]);

        return back()->with('sukses', "Layanan {$aktif->nomor} diselesaikan.");
    }

    public function batal(Antrean $antrean)
    {
        if ($antrean->status !== 'menunggu') {
            return back()->with('galat', 'Hanya antrean yang menunggu yang bisa dibatalkan.');
        }

        $antrean->update(['status' => 'batal']);

        return back()->with('sukses', "Antrean {$antrean->nomor} dibatalkan.");
    }
}
