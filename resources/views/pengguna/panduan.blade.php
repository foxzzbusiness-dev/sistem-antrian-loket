@extends('layouts.pengguna')
@section('judul', 'Panduan Antrean • Sistem Antrian Loket')

@section('isi')
<h1 class="text-2xl font-bold text-slate-900">Panduan antrean</h1>
<p class="text-sm text-slate-500 mt-1">Empat langkah untuk mendapatkan pelayanan tanpa berdiri lama di loket.</p>

<div class="grid lg:grid-cols-[1.2fr_1fr] gap-5 mt-6">
    <ol class="bg-white rounded-2xl border border-garis p-6 space-y-5">
        @foreach ([
            ['Pilih layanan', 'Buka beranda, lalu pilih kategori layanan yang sesuai dengan keperluan Anda.'],
            ['Ambil nomor antrean', 'Tekan "Ambil Nomor Antrian". Tiket Anda langsung muncul di panel Antrean Anda.'],
            ['Pantau estimasi tunggu', 'Lihat nomor yang sedang dilayani dan perkiraan waktu tunggu Anda.'],
            ['Menuju loket saat dipanggil', 'Nomor Anda akan tampil di layar dan diumumkan lewat pengeras suara. Siapkan dokumen asli.'],
        ] as $i => [$judul, $teks])
            <li class="flex gap-4">
                <span class="w-8 h-8 shrink-0 rounded-full bg-navy text-white text-sm font-bold grid place-items-center">{{ $i + 1 }}</span>
                <div><div class="text-sm font-semibold text-slate-900">{{ $judul }}</div><p class="text-xs text-slate-500 mt-1">{{ $teks }}</p></div>
            </li>
        @endforeach
    </ol>

    <section class="bg-white rounded-2xl border border-garis p-6">
        <h2 class="font-semibold text-slate-900 text-sm">Pertanyaan umum</h2>
        <div class="mt-3 divide-y divide-garis">
            @foreach ([
                ['Bolehkah mengambil lebih dari satu nomor?', 'Tidak. Satu pengunjung hanya dapat memiliki satu antrean aktif.'],
                ['Bagaimana jika nomor saya terlewat?', 'Segera temui petugas informasi agar nomor Anda dipanggil ulang.'],
                ['Apakah tiket bisa disimpan?', 'Bisa. Tekan "Simpan tiket" pada panel Antrean Anda.'],
            ] as [$tanya, $jawab])
                <details class="py-3 group">
                    <summary class="cursor-pointer text-xs font-semibold text-slate-900 list-none flex justify-between">{{ $tanya }}<span class="text-slate-400 group-open:rotate-45 transition">+</span></summary>
                    <p class="text-xs text-slate-500 mt-2">{{ $jawab }}</p>
                </details>
            @endforeach
        </div>
    </section>
</div>
@endsection