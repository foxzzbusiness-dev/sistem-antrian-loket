@extends('layouts.pengguna')
@section('judul', 'Panggilan Antrean • Sistem Antrian Loket')

@php
    $terakhir ??= [
        'nomor' => 'A-021',
        'loket' => '01',
        'layanan' => 'Administrasi Kependudukan',
    ];

    $daftarLoket ??= [
        ['loket' => '01', 'nomor' => 'A-021', 'layanan' => 'Administrasi Kependudukan', 'status' => 'melayani'],
        ['loket' => '02', 'nomor' => 'A-020', 'layanan' => 'Administrasi Kependudukan', 'status' => 'melayani'],
        ['loket' => '03', 'nomor' => 'B-012', 'layanan' => 'Perizinan', 'status' => 'melayani'],
        ['loket' => '04', 'nomor' => null, 'layanan' => 'Informasi & Pengaduan', 'status' => 'istirahat'],
    ];

    $berikutnya ??= [
        ['nomor' => 'A-022', 'estimasi' => 'Segera dipanggil'],
        ['nomor' => 'A-023', 'estimasi' => '± 5 menit'],
        ['nomor' => 'A-024', 'estimasi' => '± 10 menit'],
        ['nomor' => 'A-025', 'estimasi' => '± 15 menit'],
    ];
@endphp

@section('isi')
    <div class="min-h-full flex flex-col bg-slate-100 text-slate-900">
        <main class="flex-1 flex flex-col gap-4 p-4 lg:p-5">
            <section class="grid grid-cols-1 gap-4">
                <div class="min-h-[280px] rounded-2xl bg-blue-950 px-5 py-6 text-center text-white shadow-sm flex flex-col items-center justify-between">
                    <p class="flex items-center gap-2 text-base font-bold uppercase tracking-wider">
                        <i data-lucide="volume-2" class="w-5 h-5"></i>
                        Nomor Antrean Dipanggil
                    </p>

                    <div class="w-full my-5">
                        <p class="text-6xl md:text-7xl font-black tabular-nums tracking-tight leading-none">
                            {{ $terakhir['nomor'] ?? '---' }}
                        </p>
                        <p class="mt-3 text-lg md:text-xl font-medium text-blue-100">
                            {{ $terakhir['layanan'] ?? 'Menunggu panggilan berikutnya…' }}
                        </p>
                    </div>

                    <div class="w-full">
                        <div class="border-t border-blue-800 pt-4 flex flex-wrap items-center justify-center gap-3">
                            <span class="text-lg font-medium text-blue-100">Silakan menuju</span>
                            <span class="text-lg font-bold uppercase">Loket</span>
                            <span class="text-5xl font-black tabular-nums leading-none">
                                {{ $terakhir['loket'] ?? '-' }}
                            </span>
                            <i data-lucide="arrow-right" class="w-6 h-6 text-blue-200"></i>
                        </div>
                        <p class="mt-3 text-sm text-blue-100">
                            Mohon siapkan tiket antrean dan dokumen Anda.
                        </p>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($daftarLoket as $l)
                    @php $aktif = ($l['status'] ?? '') === 'melayani'; @endphp

                    <div class="rounded-xl bg-white border border-slate-200 shadow-sm px-4 py-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-base font-bold text-blue-950">Loket {{ $l['loket'] }}</p>
                            <span class="flex items-center gap-1.5 text-xs font-bold {{ $aktif ? 'text-green-700' : 'text-slate-500' }}">
                                <span class="w-2 h-2 rounded-full {{ $aktif ? 'bg-green-600' : 'bg-slate-400' }}"></span>
                                {{ $aktif ? 'Melayani' : 'Istirahat' }}
                            </span>
                        </div>
                        <p class="mt-2 text-4xl font-black tabular-nums leading-tight {{ $aktif ? 'text-blue-950' : 'text-slate-400' }}">
                            {{ $l['nomor'] ?? '—' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-600 truncate">{{ $l['layanan'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-xl bg-white border border-slate-200 shadow-sm px-5 py-4 flex flex-col lg:flex-row items-center gap-4">
                <div class="shrink-0 lg:pr-6 lg:border-r border-slate-200 text-center lg:text-left">
                    <p class="text-lg font-bold text-blue-950">Antrean berikutnya</p>
                    <p class="text-xs text-slate-600 mt-1">Administrasi Kependudukan</p>
                </div>

                <div class="flex-1 w-full grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    @forelse (array_slice($berikutnya, 0, 4) as $b)
                        <div>
                            <p class="text-3xl font-black tabular-nums text-blue-950">{{ $b['nomor'] }}</p>
                            <p class="mt-1 text-xs font-medium {{ $loop->first ? 'text-green-700 font-bold' : 'text-slate-600' }}">
                                {{ $b['estimasi'] }}
                            </p>
                        </div>
                    @empty
                        <p class="col-span-4 text-sm text-slate-700">Belum ada antrean berikutnya.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
@endsection
