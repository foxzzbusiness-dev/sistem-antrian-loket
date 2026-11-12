<?php

namespace Database\Seeders;

use App\Models\Antrean;
use App\Models\Layanan;
use App\Models\Loket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun petugas (kata sandi: password)
        User::forceCreate([
            'name' => 'Rina Andini', 'username' => 'rina', 'email' => 'rina@example.com',
            'password' => Hash::make('password'), 'role' => 'petugas', 'loket' => 'Loket 01',
        ]);
        User::forceCreate([
            'name' => 'Dimas Pratama', 'username' => 'dimas', 'email' => 'dimas@example.com',
            'password' => Hash::make('password'), 'role' => 'admin', 'loket' => null,
        ]);

        foreach (['Loket 01' => 'aktif', 'Loket 02' => 'aktif', 'Loket 03' => 'aktif', 'Loket 04' => 'istirahat'] as $nama => $status) {
            Loket::create(['nama' => $nama, 'status' => $status]);
        }

        $layanan = collect([
            ['A', 'Administrasi Kependudukan', 'KTP, Kartu Keluarga, dan akta', 'id-card', 'Loket 01–02', 5],
            ['B', 'Perizinan', 'Izin usaha dan nonusaha', 'file-check', 'Loket 03–04', 8],
            ['C', 'Informasi & Pengaduan', 'Konsultasi dan bantuan', 'messages-square', 'Loket 05', 4],
        ])->map(fn ($l) => Layanan::create([
            'kode' => $l[0], 'nama' => $l[1], 'deskripsi' => $l[2],
            'ikon' => $l[3], 'loket' => $l[4], 'rata_rata_menit' => $l[5],
        ]));

        $menit = 0;
        $buat = function (Layanan $l, int $urutan, string $status, ?string $loket = null) use (&$menit) {
            $menit += 3;
            Antrean::create([
                'layanan_id' => $l->id,
                'urutan' => $urutan,
                'nomor' => sprintf('%s-%03d', $l->kode, $urutan),
                'loket' => $loket,
                'status' => $status,
                'diterbitkan_at' => today()->setTime(8, 0)->addMinutes($menit),
                'dimulai_at' => $status === 'dilayani' ? now()->subMinutes(3) : null,
                'selesai_at' => $status === 'selesai' ? now()->subMinutes(10) : null,
            ]);
        };

        [$a, $b, $c] = [$layanan[0], $layanan[1], $layanan[2]];

        foreach (range(15, 20) as $n) {
            $buat($a, $n, 'selesai', 'Loket 01');
        }
        $buat($a, 21, 'dilayani', 'Loket 01');
        foreach (range(22, 24) as $n) {
            $buat($a, $n, 'menunggu');
        }
        foreach (range(1, 3) as $n) {
            $buat($b, $n, 'menunggu');
        }
        foreach (range(1, 2) as $n) {
            $buat($c, $n, 'selesai', 'Loket 05');
        }
        $buat($c, 3, 'menunggu');
    }
}
