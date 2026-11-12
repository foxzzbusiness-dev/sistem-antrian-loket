<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Layanan;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    public function beranda()
    {
        return view('pengguna.beranda', [
            'daftarLayanan' => Layanan::orderBy('kode')->get(),
        ]);
    }

    /** Buat nomor antrean baru, lalu arahkan ke halaman cetak tiket. */
    public function ambil(Layanan $layanan)
    {
        $antrean = DB::transaction(function () use ($layanan) {
            // Kunci baris layanan supaya dua pengunjung tidak mendapat nomor yang sama
            Layanan::whereKey($layanan->id)->lockForUpdate()->first();

            $urutan = (Antrean::where('layanan_id', $layanan->id)
                ->whereDate('diterbitkan_at', today())
                ->max('urutan') ?? 0) + 1;

            return Antrean::create([
                'layanan_id' => $layanan->id,
                'urutan' => $urutan,
                'nomor' => sprintf('%s-%03d', $layanan->kode, $urutan),
                'diterbitkan_at' => now(),
            ]);
        });

        return redirect()->route('tiket.cetak', $antrean);
    }

    public function cetak(Antrean $antrean)
    {
        $antrean->load('layanan');

        $menunggu = Antrean::where('layanan_id', $antrean->layanan_id)
            ->whereDate('diterbitkan_at', today())
            ->where('status', 'menunggu')
            ->where('urutan', '<', $antrean->urutan)
            ->count();

        return view('pengguna.tiket', compact('antrean', 'menunggu'));
    }
}