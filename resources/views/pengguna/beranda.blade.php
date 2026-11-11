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
        {{-- Layanan yang dipilih --}}
        <section class="bg-white rounded-2xl border border-garis p-6">
            <h2 class="font-semibold text-slate-900">Apa keperluan Anda hari ini?</h2>
            <p class="text-xs text-slate-500 mt-1">Layanan yang Anda pilih ditampilkan di bawah ini.</p>

            <form method="POST" action="{{ route('antrean.ambil') }}"
                onsubmit="konfirmasiCetak(event, @js($dipilih?->nama))">
                @csrf

                <div class="mt-5">
                    @if ($dipilih)
                        <input type="hidden" name="layanan_id" value="{{ $dipilih->id }}">

                        <div class="relative rounded-xl border border-navy bg-navy-50 ring-1 ring-navy p-4">
                            <i data-lucide="{{ $dipilih->ikon }}" class="w-5 h-5 text-slate-600"></i>
                            <div class="mt-3 text-sm font-semibold text-slate-900">{{ $dipilih->nama }}</div>
                            <div class="text-[11px] text-slate-500 mt-1">{{ $dipilih->deskripsi }}</div>
                        </div>
                    @endif
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
                    <p class="text-[11px] text-slate-500 max-w-sm">
                        Anda dapat mengambil nomor antrean untuk beberapa layanan sekaligus.
                    </p>
                    <button type="submit" @disabled(!$dipilih)
                        class="inline-flex items-center gap-2 rounded-lg bg-navy text-white text-sm font-semibold px-5 py-3 hover:bg-navy-700 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy">
                        <i data-lucide="ticket" class="w-4 h-4"></i> Ambil Nomor Antrian
                    </button>
                </div>
            </form>
        </section>

        {{-- Nomor yang baru diambil --}}
        <aside class="bg-white rounded-2xl border border-garis overflow-hidden">
            <div class="bg-navy text-white px-5 py-3.5 flex items-center justify-between text-xs font-semibold">
                <span class="flex items-center gap-2"><i data-lucide="ticket" class="w-3.5 h-3.5"></i>Nomor Antrean</span>
                @if ($tiketCetak)
                    <span class="font-normal opacity-80">{{ $tiketCetak->diterbitkan_at->translatedFormat('d M Y') }}</span>
                @endif
            </div>

            @if ($tiketCetak)
                <div id="area-cetak">
                    <div class="text-center pt-5 pb-4 px-5">
                        <span
                            class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-medium bg-emerald-50 text-emerald-700">
                            Berhasil diambil
                        </span>
                        <div class="text-6xl font-extrabold tracking-tight text-navy mt-3">{{ $tiketCetak->nomor }}</div>
                        <div class="text-[11px] text-slate-500 mt-2">{{ $tiketCetak->layanan->nama }}</div>
                        <div class="text-[11px] text-slate-500 mt-1">{{ $tiketCetak->layanan->loket }}</div>
                    </div>
                    <div class="border-t border-dashed border-garis mx-5"></div>
                    <div class="px-5 py-4 text-[10px] text-slate-500 text-center">
                        Diterbitkan pukul {{ $tiketCetak->diterbitkan_at->format('H.i') }} WIB
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i data-lucide="ticket" class="w-8 h-8 mx-auto text-slate-300"></i>
                    <p class="mt-3 text-sm font-semibold text-slate-800">Belum ada nomor diambil</p>
                    <p class="mt-1 text-xs text-slate-500">
                        Pilih layanan lalu ambil nomor antrean. Nomor Anda akan muncul di sini.
                    </p>
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
                    <div class="mt-4 aspect-video rounded-xl bg-kanvas grid place-items-center text-slate-400">
                        <i data-lucide="play-circle" class="w-9 h-9"></i>
                    </div>
        </section>

        <section class="bg-white rounded-2xl border border-garis p-6">
            <div class="flex justify-between items-baseline">
                <h2 class="font-semibold text-slate-900 text-sm">Informasi layanan</h2>
                <a href="{{ route('panduan') }}" class="text-[11px] font-semibold text-slate-800 hover:underline">Lihat
                    panduan →</a>
            </div>
            <ul class="mt-4 space-y-4">
                @foreach ([
                        ['clock', 'Jam operasional', 'Senin–Jumat, 08.00–15.00 WIB • Istirahat 12.00–13.00 WIB'],
                        ['files', 'Siapkan dokumen Anda', 'Bawa KTP asli, Kartu Keluarga, dan dokumen pendukung sesuai layanan.'],
                        ['volume-2', 'Perhatikan panggilan antrean', 'Tunggu di area pelayanan. Nomor Anda akan dipanggil melalui layar dan pengeras suara.'],
                    ] as [$ikon, $judul, $teks])
                    <li class="flex gap-3">
                        <span class="w-8 h-8 shrink-0 rounded-lg bg-kanvas grid place-items-center text-slate-600">
                            <i data-lucide="{{ $ikon }}" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <div class="text-xs font-semibold text-slate-900">{{ $judul }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $teks }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    {{-- Konfirmasi sebelum ambil nomor --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function konfirmasiCetak(e, nama) {
            e.preventDefault();
            const form = e.target;

            Swal.fire({
                title: 'Cetak nomor antrean?',
                text: `Nomor untuk layanan ${nama} akan dibuat dan dicetak.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, cetak',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#14365a',
            }).then((hasil) => {
                if (hasil.isConfirmed) form.submit();
            });
        }
    </script>

@endsection