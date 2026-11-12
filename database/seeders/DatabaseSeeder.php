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
        // Akun petugas dan admin (kata sandi semuanya: password)
        $n = 0;
        foreach ([
            ['Rina Andini', 'rina', 'petugas', 'Loket 01', true],
            ['Andi Wijaya', 'andi', 'petugas', 'Loket 02', true],
            ['Sari Dewi', 'sari', 'petugas', 'Loket 03', true],
            ['Budi Hartono', 'budi', 'petugas', null, false],
            ['Dimas Pratama', 'dimas', 'admin', null, true],
        ] as [$nama, $username, $role, $loket, $aktif]) {
            User::forceCreate([
                'name' => $nama,
                'username' => $username,
                'kode' => $role === 'petugas' ? sprintf('PTO-%03d', ++$n) : null,
                'email' => "{$username}@example.com",
                'password' => Hash::make('password'),
                'role' => $role,
                'loket' => $loket,
                'shift_mulai' => '08:00',
                'shift_selesai' => '16:00',
                'aktif' => $aktif,
            ]);
        }

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