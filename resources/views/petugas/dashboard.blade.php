@extends('layouts.petugas')
@section('judul', 'Dashboard Petugas')

@section('isi')
    @php
        $jam = now()->hour;
        $sapaan = $jam < 11 ? 'pagi' : ($jam < 15 ? 'siang' : ($jam < 19 ? 'sore' : 'malam'));
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Selamat {{ $sapaan }}, {{ explode(' ', $petugas->name)[0] }}</h1>
            <p class="text-xs text-slate-500 mt-1">Berikut ringkasan antrean dan aktivitas pelayanan hari ini.</p>
        </div>
        <span
            class="inline-flex items-center gap-2 rounded-lg border border-garis bg-white px-3.5 py-2 text-xs font-medium text-slate-700">
            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>Hari ini
        </span>
    </div>

    {{-- Statistik --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-5">
        @foreach ([
                ['Total antrean', $stat['total'], 'hari ini', 'ticket', 'bg-slate-100 text-slate-600'],
                ['Menunggu', $stat['menunggu'], 'antrean', 'clock', 'bg-amber-50 text-amber-600'],
                ['Sedang dilayani', $stat['dilayani'], 'loket aktif', 'headset', 'bg-sky-50 text-sky-600'],
                ['Selesai', $stat['selesai'], 'antrean', 'circle-check', 'bg-emerald-50 text-emerald-600'],
            ] as [$label, $nilai, $satuan, $ikon, $warna])
            <div class="bg-white rounded-2xl border border-garis p-5 flex items-start justify-between">
                <div>
                    <div class="text-[11px] text-slate-500">{{ $label }}</div>
                    <div class="mt-2 text-3xl font-bold text-slate-900">{{ $nilai }} <span
                            class="text-[10px] font-normal text-slate-400">{{ $satuan }}</span></div>
                </div>
                <span class="w-8 h-8 rounded-lg grid place-items-center {{ $warna }}"><i data-lucide="{{ $ikon }}"
                        class="w-4 h-4"></i></span>
            </div>
        @endforeach
    </div>

    {{-- Sedang dilayani --}}
    <section class="bg-white rounded-2xl border border-garis p-6 mt-5 flex flex-wrap items-center justify-between gap-6">
        <div class="flex items-center gap-8">
            <div>
                <div class="text-[10px] font-semibold tracking-wide text-slate-400">SEDANG DILAYANI</div>
                <div class="text-4xl font-extrabold tracking-tight text-navy mt-1">{{ $aktif->nomor ?? '—' }}</div>
            </div>
            <div class="text-[11px] text-slate-500 space-y-1">
                <div class="text-sm font-semibold text-slate-900">{{ $petugas->loket ?? 'Belum ada loket' }}</div>
                @if ($aktif)
                    @if ($aktif->dimulai_at)
                        <div>Mulai {{ $aktif->dimulai_at->format('H.i') }} • <span id="durasi"
                                data-mulai="{{ $aktif->dimulai_at->timestamp }}">00:00</span> menit</div>
                    @else
                        <div>Sedang dilayani</div>
                    @endif
                @else
                    <div>Belum ada antrean yang dilayani</div>
                @endif
                <div>Berikutnya: {{ $berikutnya->nomor ?? '—' }}</div>
            </div>
        </div>

        <div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('petugas.panggil') }}">@csrf
                    <button @disabled($aktif || !$berikutnya)
                        class="inline-flex items-center gap-2 rounded-lg bg-navy text-white text-xs font-semibold px-4 py-2.5 hover:bg-navy-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="megaphone" class="w-3.5 h-3.5"></i>Panggil Antrean Berikutnya
                    </button>
                </form>
                <form method="POST" action="{{ route('petugas.ulang') }}">@csrf
                    <button @disabled(!$aktif)
                        class="inline-flex items-center gap-2 rounded-lg border border-garis bg-white text-xs font-semibold text-slate-700 px-4 py-2.5 hover:bg-kanvas disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="volume-2" class="w-3.5 h-3.5"></i>Panggil Ulang
                    </button>
                </form>
                <form method="POST" action="{{ route('petugas.selesai') }}">@csrf
                    <button @disabled(!$aktif)
                        class="inline-flex items-center gap-2 rounded-lg border border-garis bg-white text-xs font-semibold text-slate-700 px-4 py-2.5 hover:bg-kanvas disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>Selesaikan Layanan
                    </button>
                </form>
            </div>
            <p class="mt-2 text-right text-[10px] text-slate-400">Selesaikan layanan aktif sebelum memanggil antrean
                berikutnya.</p>
        </div>
    </section>

    {{-- Daftar antrean --}}
    <section class="bg-white rounded-2xl border border-garis mt-5">
        <div class="flex flex-wrap items-center justify-between gap-3 p-5">
            <div class="flex items-baseline gap-2">
                <h2 class="text-sm font-semibold text-slate-900">Daftar antrean</h2>
                <span class="text-[11px] text-slate-400">{{ $stat['menunggu'] }} menunggu</span>
            </div>
            <form method="GET" class="flex gap-2">
                <div class="relative">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor antrean..."
                        class="rounded-lg border border-garis pl-9 pr-3 py-2 text-xs w-48 focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/20">
                </div>
                <select name="layanan" onchange="this.form.submit()"
                    class="rounded-lg border border-garis px-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-navy">
                    <option value="">Semua layanan</option>
                    @foreach ($daftarLayanan as $l)
                        <option value="{{ $l->id }}" @selected(request('layanan') == $l->id)>{{ $l->nama }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-[11px] text-slate-400">
                    <tr>
                        <th class="px-5 py-2.5 font-normal">Nomor antrean</th>
                        <th class="px-3 py-2.5 font-normal">Layanan</th>
                        <th class="px-3 py-2.5 font-normal">Waktu masuk</th>
                        <th class="px-3 py-2.5 font-normal">Loket</th>
                        <th class="px-3 py-2.5 font-normal">Status</th>
                        <th class="px-5 py-2.5 font-normal text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftar as $a)
                        @php
                            $badge = match ($a->status) {
                                'dilayani' => ['Dilayani', 'bg-emerald-50 text-emerald-700'],
                                'menunggu' => ['Menunggu', 'bg-amber-50 text-amber-700'],
                                'selesai' => ['Selesai', 'bg-slate-100 text-slate-600'],
                                default => ['Dibatalkan', 'bg-red-50 text-red-700'],
                            };
                        @endphp
                        <tr class="border-t border-garis {{ $a->status === 'dilayani' ? 'bg-kanvas' : '' }}">
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $a->nomor }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $a->layanan->nama }}</td>
                            <td class="px-3 py-3 text-slate-500">{{ $a->diterbitkan_at->format('H.i') }} WIB</td>
                            <td class="px-3 py-3 text-slate-500">{{ $a->loket ?? '—' }}</td>
                            <td class="px-3 py-3"><span
                                    class="rounded-full px-2.5 py-0.5 text-[10px] font-medium {{ $badge[1] }}">{{ $badge[0] }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if ($a->status === 'menunggu')
                                    <form method="POST" action="{{ route('petugas.batal', $a) }}"
                                        onsubmit="return confirm('Batalkan antrean {{ $a->nomor }}?')">
                                        @csrf
                                        <button title="Batalkan antrean" aria-label="Batalkan antrean {{ $a->nomor }}"
                                            class="text-slate-400 hover:text-red-600"><i data-lucide="x"
                                                class="w-4 h-4"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">Tidak ada antrean yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div
            class="flex flex-wrap items-center justify-between gap-3 border-t border-garis px-5 py-3 text-[11px] text-slate-400">
            <span>Menampilkan {{ $daftar->firstItem() ?? 0 }}–{{ $daftar->lastItem() ?? 0 }} dari {{ $daftar->total() }}
                antrean hari ini</span>
            <div>{{ $daftar->links() }}</div>
        </div>
    </section>
@endsection

@push('skrip')
    <script>
        const durasi = document.getElementById('durasi');
        if (durasi) {
            const mulai = Number(durasi.dataset.mulai);
            const tampil = () => {
                const s = Math.max(0, Math.floor(Date.now() / 1000) - mulai);
                durasi.textContent = String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
            };
            tampil();
            setInterval(tampil, 1000);
        }
    </script>
@endpush