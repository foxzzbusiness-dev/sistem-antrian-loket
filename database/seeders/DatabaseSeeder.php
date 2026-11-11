<?php

namespace Database\Seeders;

use App\Models\Antrean;
use App\Models\Layanan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = collect([
            ['A', 'Administrasi Kependudukan', 'KTP, Kartu Keluarga, dan akta', 'id-card', 'Loket 01–02', 5],
            ['B', 'Perizinan', 'Izin usaha dan nonusaha', 'file-check', 'Loket 03–04', 8],
            ['C', 'Informasi & Pengaduan', 'Konsultasi dan bantuan', 'messages-square', 'Loket 05', 4],
        ])->map(fn ($l) => Layanan::create([
            'kode' => $l[0], 'nama' => $l[1], 'deskripsi' => $l[2],
            'ikon' => $l[3], 'loket' => $l[4], 'rata_rata_menit' => $l[5],
        ]));

        $a = $layanan[0];

        // Antrean berjalan: A-020 selesai, A-021 dilayani, A-022..A-024 menunggu
        foreach (range(20, 24) as $n) {
            Antrean::create([
                'token' => null,
                'layanan_id' => $a->id,
                'urutan' => $n,
                'nomor' => sprintf('A-%03d', $n),
                'loket' => 'Loket 01',
                'status' => $n === 20 ? 'selesai' : ($n === 21 ? 'dilayani' : 'menunggu'),
                'diterbitkan_at' => now()->subMinutes(70 - $n),
            ]);
        }
    }
}