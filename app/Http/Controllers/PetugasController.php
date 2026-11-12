<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
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

    public function portal(Request $request)
    {
        $petugas = $request->user();

        // Sinyal "portal sedang dibuka" untuk pemantauan admin
        User::whereKey($petugas->id)->update(['terakhir_aktif_at' => now()]);

        return view('petugas.portal', [
            'petugas' => $petugas,
            'aktif' => $this->aktifDi($petugas->loket),
            'menunggu' => $this->hariIni()->with('layanan')->where('status', 'menunggu')
                ->orderBy('diterbitkan_at')->orderBy('id')->get(),
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

    /** Batalkan antrean menunggu, atau antrean yang sedang dilayani di loket sendiri. */
    public function batal(Request $request, Antrean $antrean)
    {
        $sah = $antrean->status === 'menunggu'
            || ($antrean->status === 'dilayani' && $antrean->loket === $request->user()->loket);

        if (! $sah) {
            return back()->with('galat', 'Antrean ini tidak bisa dibatalkan.');
        }

        $antrean->update(['status' => 'batal', 'selesai_at' => now()]);

        return back()->with('sukses', "Antrean {$antrean->nomor} dibatalkan.");
    }
}