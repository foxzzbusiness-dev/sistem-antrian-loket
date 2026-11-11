@extends('layouts.pengguna')
@section('judul', 'Beranda • Sistem Antrian Loket')

@section('isi')
@php
    \Carbon\Carbon::setLocale('id');
    $jam = now()->hour;
    $sapaan = $jam < 11 ? 'pagi' : ($jam < 15 ? 'siang' : ($jam < 19 ? 'sore' : 'malam'));
@endphp

<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Selamat {{ $sapaan }} 👋</h1>
        <p class="text-sm text-slate-500 mt-1">Pilih layanan Anda dan pantau antrean dengan lebih nyaman.</p>
    </div>
    <div class="text-right text-xs text-slate-500">{{ now()->translatedFormat('l, j F Y') }}</div>
</div>

<div class="grid lg:grid-cols-[1fr_340px] gap-5">
    {{-- Pilih layanan --}}
    <section class="bg-white rounded-2xl border border-garis p-6">
        <h2 class="font-semibold text-slate-900">Apa keperluan Anda hari ini?</h2>
        <p class="text-xs text-slate-500 mt-1">Silakan pilih salah satu kategori layanan di bawah ini.</p>

        <form method="POST" action="{{ route('antrean.ambil') }}">
            @csrf
            <div class="grid sm:grid-cols-3 gap-3 mt-5">
                @foreach ($daftarLayanan as $l)
                    <label class="relative cursor-pointer rounded-xl border border-garis p-4 transition hover:border-slate-300 has-[:checked]:border-navy has-[:checked]:bg-navy-50 has-[:checked]:ring-1 has-[:checked]:ring-navy">
                        <input type="radio" name="layanan_id" value="{{ $l->id }}" class="absolute top-3 right-3 accent-[#14365a]" @checked($dipilih->id === $l->id)>
                        <i data-lucide="{{ $l->ikon }}" class="w-5 h-5 text-slate-600"></i>
                        <div class="mt-3 text-sm font-semibold text-slate-900">{{ $l->nama }}</div>
                        <div class="text-[11px] text-slate-500 mt-1">{{ $l->deskripsi }}</div>
                    </label>
                @endforeach
            </div>

                <div class="mt-4 rounded-lg bg-kanvas px-4 py-2.5 text-xs text-slate-600 flex items-center gap-2">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                @if ($dipilih)
                {{ $dipilih->loket }} <span>•</span> Rata-rata pelayanan {{ $dipilih->rata_rata_menit }} menit / orang
                @else
                Layanan belum tersedia. Silakan hubungi petugas.
                @endif
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
                <p class="text-[11px] text-slate-500 max-w-sm">Sudah punya tiket? Tiket aktif Anda tetap tersimpan. Satu pengunjung hanya dapat memiliki satu antrean aktif.</p>
                <button type="submit" @disabled($tiket) class="inline-flex items-center gap-2 rounded-lg bg-navy text-white text-sm font-semibold px-5 py-3 hover:bg-navy-700 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy">
                    <i data-lucide="ticket" class="w-4 h-4"></i> Ambil Nomor Antrian
                </button>
            </div>
        </form>
    </section>

    {{-- Antrean Anda --}}
    <aside class="bg-white rounded-2xl border border-garis overflow-hidden">
        <div class="bg-navy text-white px-5 py-3.5 flex items-center justify-between text-xs font-semibold">
            <span class="flex items-center gap-2"><i data-lucide="ticket" class="w-3.5 h-3.5"></i>Antrean Anda</span>
            @if ($tiket)<span class="font-normal opacity-80">{{ $tiket->diterbitkan_at->translatedFormat('d M Y') }}</span>@endif
        </div>

        @if ($tiket)
            <div class="text-center pt-5 pb-4 px-5">
                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-medium {{ $tiket->status === 'dilayani' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                    {{ $tiket->status === 'dilayani' ? 'Sedang dilayani' : 'Menunggu' }}
                </span>
                <div class="text-6xl font-extrabold tracking-tight text-navy mt-3">{{ $tiket->nomor }}</div>
                <div class="text-[11px] text-slate-500 mt-2">{{ $tiket->layanan->nama }}</div>
            </div>
            <div class="border-t border-dashed border-garis mx-5"></div>
            <div class="grid grid-cols-2 gap-4 px-5 py-4 text-[11px] text-slate-500">
                <div>Sedang dilayani
                    <div class="text-xl font-bold text-slate-900 mt-1">{{ $sedangDilayani->nomor ?? '—' }}</div>
                    <div class="mt-0.5">{{ $sedangDilayani->loket ?? '' }}</div>
                </div>
                <div class="border-l border-garis pl-4">Estimasi tunggu
                    <div class="text-xl font-bold text-slate-900 mt-1">± {{ $estimasi }} menit</div>
                    <div class="mt-0.5">{{ $sebelumAnda }} antrean sebelum Anda</div>
                </div>
            </div>
            <div class="px-5 pb-4 flex items-center justify-between text-[10px] text-navy">
                <span>Diterbitkan pukul {{ $tiket->diterbitkan_at->format('H.i') }} WIB</span>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 font-semibold text-slate-800 hover:underline"><i data-lucide="download" class="w-3 h-3"></i>Simpan tiket</button>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i data-lucide="ticket" class="w-8 h-8 mx-auto text-slate-300"></i>
                <p class="mt-3 text-sm font-semibold text-slate-800">Belum ada antrean aktif</p>
                <p class="mt-1 text-xs text-slate-500">Pilih layanan lalu ambil nomor antrean. Tiket Anda akan muncul di sini.</p>
            </div>
        @endif
    </aside>
</div>

<div class="grid lg:grid-cols-[1fr_1.3fr] gap-5 mt-5">
    <section class="bg-white rounded-2xl border border-garis p-6 min-h-[240px]">
        <div class="flex justify-between items-baseline">
            <h2 class="font-semibold text-slate-900 text-sm">Kenali layanan kami</h2>
            <span class="text-[11px] text-slate-500">Video informasi</span>
        </div>
        {{-- Ganti blok ini dengan <iframe>/<video> saat materi video sudah ada --}}
        <div class="mt-4 aspect-video rounded-xl bg-kanvas grid place-items-center text-slate-400"><i data-lucide="play-circle" class="w-9 h-9"></i></div>
    </section>

    <section class="bg-white rounded-2xl border border-garis p-6">
        <div class="flex justify-between items-baseline">
            <h2 class="font-semibold text-slate-900 text-sm">Informasi layanan</h2>
            <a href="{{ route('panduan') }}" class="text-[11px] font-semibold text-slate-800 hover:underline">Lihat panduan →</a>
        </div>
        <ul class="mt-4 space-y-4">
            @foreach ([
                ['clock', 'Jam operasional', 'Senin–Jumat, 08.00–15.00 WIB • Istirahat 12.00–13.00 WIB'],
                ['files', 'Siapkan dokumen Anda', 'Bawa KTP asli, Kartu Keluarga, dan dokumen pendukung sesuai layanan.'],
                ['volume-2', 'Perhatikan panggilan antrean', 'Tunggu di area pelayanan. Nomor Anda akan dipanggil melalui layar dan pengeras suara.'],
            ] as [$ikon, $judul, $teks])
                <li class="flex gap-3">
                    <span class="w-8 h-8 shrink-0 rounded-lg bg-kanvas grid place-items-center text-slate-600"><i data-lucide="{{ $ikon }}" class="w-4 h-4"></i></span>
                    <div><div class="text-xs font-semibold text-slate-900">{{ $judul }}</div><div class="text-[11px] text-slate-500 mt-0.5">{{ $teks }}</div></div>
                </li>
            @endforeach
        </ul>
    </section>
</div>
@endsection