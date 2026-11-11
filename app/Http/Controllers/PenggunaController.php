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

        $tiketCetak = session('tiket_cetak')
            ? Antrean::with('layanan')->find(session('tiket_cetak'))
            : null;

        return view('pengguna.beranda', compact('daftarLayanan', 'dipilih', 'tiketCetak'));
    }

    public function ambil(Request $request)
    {
        $data = $request->validate(['layanan_id' => 'required|exists:layanan,id']);

        $layanan = Layanan::findOrFail($data['layanan_id']);
        $urutan = (Antrean::where('layanan_id', $layanan->id)->max('urutan') ?? 0) + 1;

        $tiket = Antrean::create([
            'token' => $this->token($request),
            'layanan_id' => $layanan->id,
            'urutan' => $urutan,
            'nomor' => sprintf('%s-%03d', $layanan->kode, $urutan),
            'diterbitkan_at' => now(),
        ]);

        return redirect()->route('beranda', ['layanan' => $layanan->id])->with('tiket_cetak', $tiket->id);
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