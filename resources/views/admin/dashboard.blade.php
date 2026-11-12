@extends('layouts.admin')
@section('judul', 'Dashboard Admin')
@section('judul_halaman', 'Pemantauan petugas')

@section('isi')
<div id="pemantauan">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pantau petugas yang sedang bertugas</h1>
            <p class="text-xs text-slate-500 mt-1">Data diperbarui otomatis setiap 5 detik. Petugas tampil online selama portalnya terbuka.</p>
        </div>
        <span class="inline-flex items-center gap-2 rounded-lg border border-garis bg-white px-3.5 py-2 text-xs text-slate-600">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Diperbarui {{ now()->format('H.i.s') }}
        </span>
            {{-- Tiket hari ini --}}
    <section class="bg-white rounded-2xl border border-garis mt-5">
        <div class="flex items-baseline justify-between p-5">
            <h2 class="text-sm font-semibold text-slate-900">Tiket hari ini</h2>
            <span class="text-[11px] text-slate-400">{{ $tiket->total() }} tiket</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-[11px] text-slate-400">
                    <tr>
                        <th class="px-5 py-2.5 font-normal">Nomor</th>
                        <th class="px-3 py-2.5 font-normal">Layanan</th>
                        <th class="px-3 py-2.5 font-normal">Waktu masuk</th>
                        <th class="px-3 py-2.5 font-normal">Loket</th>
                        <th class="px-3 py-2.5 font-normal">Status</th>
                        <th class="px-5 py-2.5 font-normal text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($tiket as $a)
                    @php
                        $b = match ($a->status) {
                            'dilayani' => ['Dilayani', 'bg-emerald-50 text-emerald-700'],
                            'menunggu' => ['Menunggu', 'bg-amber-50 text-amber-700'],
                            'selesai' => ['Selesai', 'bg-slate-100 text-slate-600'],
                            default => ['Dibatalkan', 'bg-red-50 text-red-700'],
                        };
                    @endphp
                    <tr class="border-t border-garis">
                        <td class="px-5 py-3 font-semibold text-slate-900">{{ $a->nomor }}</td>
                        <td class="px-3 py-3 text-slate-700">{{ $a->layanan->nama }}</td>
                        <td class="px-3 py-3 text-slate-500">{{ $a->diterbitkan_at->format('H.i') }} WIB</td>
                        <td class="px-3 py-3 text-slate-500">{{ $a->loket ?? '—' }}</td>
                        <td class="px-3 py-3"><span class="rounded-full px-2.5 py-0.5 text-[10px] font-medium {{ $b[1] }}">{{ $b[0] }}</span></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-3 text-slate-400">
                                <a href="{{ route('tiket.cetak', $a) }}" target="_blank" title="Buka tiket untuk dicetak ulang" aria-label="Cetak ulang {{ $a->nomor }}" class="hover:text-navy"><i data-lucide="printer" class="w-4 h-4"></i></a>
                                @if (in_array($a->status, ['menunggu', 'dilayani']))
                                    <form method="POST" action="{{ route('admin.tiket.batal', $a) }}" onsubmit="return confirm('Batalkan tiket {{ $a->nomor }}?')">@csrf
                                        <button title="Batalkan tiket" aria-label="Batalkan {{ $a->nomor }}" class="hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada tiket hari ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-garis px-5 py-3 text-[11px] text-slate-400">{{ $tiket->links() }}</div>
    </section>
    </div>

    {{-- Ringkasan --}}
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-5">
        @foreach ([
            ['Petugas online', $stat['online'], 'dari '.$stat['total_petugas'].' petugas', 'user-check', 'bg-sky-50 text-sky-600'],
            ['Sedang melayani', $stat['melayani'], 'loket aktif', 'headset', 'bg-emerald-50 text-emerald-600'],
            ['Antrean menunggu', $stat['menunggu'], $stat['terlama'] !== null ? 'terlama '.$stat['terlama'].' menit' : 'tidak ada antrean', 'clock', 'bg-amber-50 text-amber-600'],
            ['Selesai hari ini', $stat['selesai'], 'antrean', 'circle-check', 'bg-slate-100 text-slate-600'],
        ] as [$label, $nilai, $ket, $ikon, $warna])
            <div class="bg-white rounded-2xl border border-garis p-5 flex items-start justify-between">
                <div>
                    <div class="text-[11px] text-slate-500">{{ $label }}</div>
                    <div class="mt-2 text-3xl font-bold text-slate-900">{{ $nilai }}</div>
                    <div class="mt-1 text-[10px] text-slate-400">{{ $ket }}</div>
                </div>
                <span class="w-8 h-8 rounded-lg grid place-items-center {{ $warna }}"><i data-lucide="{{ $ikon }}" class="w-4 h-4"></i></span>
            </div>
        @endforeach
    </div>

    <div class="grid xl:grid-cols-[1fr_280px] gap-5 mt-5 items-start">

        {{-- Tabel petugas --}}
        <section class="bg-white rounded-2xl border border-garis">
            <div class="p-5">
                <h2 class="text-sm font-semibold text-slate-900">Petugas di loket</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-[11px] text-slate-400">
                        <tr>
                            <th class="px-5 py-2.5 font-normal">Petugas</th>
                            <th class="px-3 py-2.5 font-normal">Loket</th>
                            <th class="px-3 py-2.5 font-normal">Status</th>
                            <th class="px-3 py-2.5 font-normal">Sedang dilayani</th>
                            <th class="px-3 py-2.5 font-normal">Selesai</th>
                            <th class="px-3 py-2.5 font-normal">Rata-rata</th>
                            <th class="px-5 py-2.5 font-normal">Terakhir aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($petugas as $p)
                        @php
                            $badge = [
                                'melayani' => ['Melayani', 'bg-emerald-50 text-emerald-700', 'bg-emerald-500'],
                                'siaga' => ['Siaga', 'bg-sky-50 text-sky-700', 'bg-sky-500'],
                                'offline' => ['Offline', 'bg-slate-100 text-slate-500', 'bg-slate-400'],
                            ][$p->status];
                        @endphp
                        <tr class="border-t border-garis">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-full bg-navy-50 text-navy text-[11px] font-bold grid place-items-center">{{ collect(explode(' ', $p->nama))->map(fn ($k) => mb_substr($k, 0, 1))->take(2)->join('') }}</span>
                                    <span class="font-semibold text-slate-900">{{ $p->nama }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-slate-600">{{ $p->loket }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-medium {{ $badge[1] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge[2] }}"></span>{{ $badge[0] }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                @if ($p->nomor)
                                    <div class="font-bold text-slate-900">{{ $p->nomor }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $p->layanan }} • {{ $p->durasi }} menit</div>
                                    @if ($p->status === 'offline')<div class="text-[10px] text-red-600">Portal tertutup saat melayani</div>@endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-slate-700">{{ $p->selesai }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $p->rata !== null ? $p->rata.' menit' : '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $p->terakhir ? $p->terakhir->format('H.i.s') : 'Belum pernah' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">Belum ada akun petugas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Menunggu per layanan --}}
        <section class="bg-white rounded-2xl border border-garis p-5">
            <h2 class="text-sm font-semibold text-slate-900">Menunggu per layanan</h2>
            <ul class="mt-4 space-y-3">
                @foreach ($perLayanan as $l)
                    <li class="flex items-center justify-between rounded-xl bg-kanvas px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-white grid place-items-center text-xs font-bold text-navy">{{ $l->kode }}</span>
                            <span class="text-xs font-medium text-slate-800">{{ $l->nama }}</span>
                        </div>
                        <span class="text-sm font-bold text-slate-900">{{ $l->menunggu_count }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</div>
@endsection

@push('skrip')
<script>
    // Perbarui bagian pemantauan tiap 5 detik tanpa memuat ulang seluruh halaman
    const area = document.getElementById('pemantauan');
    setInterval(async () => {
        if (document.hidden) return;
        try {
            const r = await fetch(location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (r.redirected && r.url.includes('/login')) { location.reload(); return; }
            if (!r.ok) return;
            const doc = new DOMParser().parseFromString(await r.text(), 'text/html');
            const baru = doc.getElementById('pemantauan');
            if (baru) { area.innerHTML = baru.innerHTML; lucide.createIcons(); }
        } catch (e) { /* coba lagi 5 detik kemudian */ }
    }, 5000);
</script>
@endpush