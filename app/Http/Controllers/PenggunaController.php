<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PenggunaController extends Controller
{
    /** Penanda pengunjung tanpa login: token acak yang disimpan di session browser. */
    private function token(Request $request): string
    {
        if (! $request->session()->has('token_antrean')) {
            $request->session()->put('token_antrean', (string) Str::uuid());
        }

        return $request->session()->get('token_antrean');
    }

    public function beranda(Request $request)
    {
        $daftarLayanan = Layanan::orderBy('kode')->get();
        $dipilih = $daftarLayanan->firstWhere('id', (int) $request->query('layanan')) ?? $daftarLayanan->first();

        $tiket = Antrean::with('layanan')
            ->where('token', $this->token($request))
            ->aktif()->latest('id')->first();

        $sedangDilayani = $estimasi = $sebelumAnda = null;

        if ($tiket) {
            $sedangDilayani = Antrean::where('layanan_id', $tiket->layanan_id)
                ->where('status', 'dilayani')->latest('urutan')->first();
            $sebelumAnda = Antrean::where('layanan_id', $tiket->layanan_id)
                ->where('urutan', '<', $tiket->urutan)->aktif()->count();
            $estimasi = $sebelumAnda * $tiket->layanan->rata_rata_menit;
        }

        return view('pengguna.beranda', compact(
            'daftarLayanan', 'dipilih', 'tiket', 'sedangDilayani', 'estimasi', 'sebelumAnda'
        ));
    }

    public function ambil(Request $request)
    {
        $token = $this->token($request);
        $data = $request->validate(['layanan_id' => 'required|exists:layanan,id']);

        if (Antrean::where('token', $token)->aktif()->exists()) {
            return back()->with('galat', 'Anda masih memiliki tiket aktif. Selesaikan dulu sebelum mengambil nomor baru.');
        }

        $layanan = Layanan::findOrFail($data['layanan_id']);
        $urutan = (Antrean::where('layanan_id', $layanan->id)->max('urutan') ?? 0) + 1;

        Antrean::create([
            'token' => $token,
            'layanan_id' => $layanan->id,
            'urutan' => $urutan,
            'nomor' => sprintf('%s-%03d', $layanan->kode, $urutan),
            'diterbitkan_at' => now(),
        ]);

        return redirect()->route('beranda')->with('sukses', 'Nomor antrean berhasil diambil.');
    }

    public function layanan()
    {
        return view('pengguna.layanan', [
            'daftarLayanan' => Layanan::orderBy('kode')->get(),
        ]);
    }

    public function panduan()
    {
        return view('pengguna.panduan');
    }
}