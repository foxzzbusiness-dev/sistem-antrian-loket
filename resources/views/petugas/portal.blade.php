<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Loket Petugas • Sistem Antrian Loket</title>
    @include('partials.aset')
</head>
<body class="bg-kanvas font-sans text-slate-800 antialiased min-h-screen flex flex-col">

<header class="bg-navy">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between gap-4">
        <h1 class="text-lg sm:text-xl font-bold text-blue-300">Portal loket petugas</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('beranda') }}" class="hidden sm:inline-flex rounded-lg border border-white/30 px-3.5 py-2 text-xs font-medium text-white hover:bg-white/10">Ke beranda utama</a>
            <span class="text-sm text-white/90">{{ $petugas->name }}</span>
            <form method="POST" action="{{ route('keluar') }}">
                @csrf
                <button class="rounded-lg border border-red-400 px-3.5 py-2 text-xs font-medium text-red-300 hover:bg-red-500/10">Log out</button>
            </form>
        </div>
    </div>
</header>

<main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
    @if (session('sukses'))
        <div class="mb-5 rounded-lg bg-emerald-50 text-emerald-800 text-sm px-4 py-3 border border-emerald-200">{{ session('sukses') }}</div>
    @endif
    @if (session('galat'))
        <div class="mb-5 rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-200">{{ session('galat') }}</div>
    @endif

    <div id="portal">
        @if (! $petugas->loket)
            <div class="rounded-3xl bg-white border border-garis px-6 py-16 text-center">
                <p class="text-sm font-semibold text-slate-800">Akun Anda belum ditempatkan di loket</p>
                <p class="mt-1 text-xs text-slate-500">Hubungi administrator untuk mengatur loket Anda.</p>
            </div>
        @else
        <div class="grid lg:grid-cols-[1.7fr_1fr] gap-5 items-start">

            {{-- Status loket --}}
            <section class="rounded-3xl bg-white border border-garis p-6 sm:p-8">
                <h2 class="text-center text-sm font-semibold text-slate-900">Status {{ $petugas->loket }} dan nomor yang dilayani</h2>

                <div class="mt-5 rounded-2xl bg-navy text-white text-center py-10">
                    <div class="text-[11px] text-white/60">{{ $aktif ? 'Sedang dilayani' : 'Belum ada antrean yang dilayani' }}</div>
                    <div class="mt-2 text-6xl sm:text-7xl font-extrabold tracking-tight">{{ $aktif->nomor ?? '—' }}</div>
                    @if ($aktif)
                        <div class="mt-3 text-xs text-white/80">{{ $aktif->layanan->nama }}</div>
                        <div class="mt-1 text-[11px] text-white/60">
                            Mulai {{ $aktif->dimulai_at->format('H.i') }} •
                            <span id="durasi" data-mulai="{{ $aktif->dimulai_at->timestamp }}">00:00</span> menit
                        </div>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <form method="POST" action="{{ route('petugas.panggil') }}">@csrf
                        <button @disabled($aktif || $menunggu->isEmpty())
                                class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-navy text-white text-xs font-semibold px-4 py-3 hover:bg-navy-700 disabled:opacity-40 disabled:cursor-not-allowed">
                            <i data-lucide="megaphone" class="w-3.5 h-3.5"></i>Panggil Berikutnya
                        </button>
                    </form>

                    <form method="POST" action="{{ route('petugas.ulang') }}">@csrf
                        <button @disabled(! $aktif)
                                class="w-full inline-flex items-center justify-center gap-2 rounded-full border border-garis bg-white text-slate-700 text-xs font-semibold px-4 py-3 hover:bg-kanvas disabled:opacity-40 disabled:cursor-not-allowed">
                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i>Panggil Ulang
                        </button>
                    </form>

                    <form method="POST" action="{{ route('petugas.selesai') }}">@csrf
                        <button @disabled(! $aktif)
                                class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 text-white text-xs font-semibold px-4 py-3 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>Selesaikan
                        </button>
                    </form>

                    @if ($aktif)
                        <form method="POST" action="{{ route('petugas.batal', $aktif) }}" onsubmit="return confirm('Batalkan antrean {{ $aktif->nomor }}?')">@csrf
                            <button class="w-full inline-flex items-center justify-center gap-2 rounded-full border border-red-300 bg-white text-red-600 text-xs font-semibold px-4 py-3 hover:bg-red-50">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>Batalkan
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full inline-flex items-center justify-center gap-2 rounded-full border border-red-300 bg-white text-red-600 text-xs font-semibold px-4 py-3 opacity-40 cursor-not-allowed">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>Batalkan
                        </button>
                    @endif
                </div>

                <p class="mt-4 text-center text-[11px] text-slate-400">Selesaikan layanan yang aktif sebelum memanggil antrean berikutnya.</p>
            </section>

            {{-- Antrean menunggu --}}
            <section class="rounded-3xl bg-white border border-garis p-6">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">Antrean menunggu</h2>
                    <span class="text-[11px] text-slate-400">{{ $menunggu->count() }} antrean</span>
                </div>

                @if ($menunggu->isEmpty())
                    <div class="mt-6 rounded-2xl bg-kanvas px-4 py-10 text-center">
                        <p class="text-xs font-semibold text-slate-700">Belum ada antrean menunggu</p>
                        <p class="mt-1 text-[11px] text-slate-500">Tiket baru dari pengunjung akan muncul di sini.</p>
                    </div>
                @else
                    <ul class="mt-4 space-y-2.5 max-h-[28rem] overflow-y-auto pr-1">
                        @foreach ($menunggu as $i => $a)
                            <li class="rounded-xl px-4 py-3 flex items-center justify-between {{ $i === 0 ? 'bg-navy-50 ring-1 ring-navy/30' : 'bg-kanvas' }}">
                                <div>
                                    <div class="text-base font-bold text-slate-900">{{ $a->nomor }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $a->layanan->nama }}</div>
                                </div>
                                <div class="text-right text-[11px] text-slate-500">
                                    @if ($i === 0)<div class="font-semibold text-navy">Berikutnya</div>@endif
                                    <div>Masuk {{ $a->diterbitkan_at->format('H.i') }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
        @endif
    </div>
</main>

<script>
    lucide.createIcons();

    const portal = document.getElementById('portal');
    let bekerja = false;

    // Hindari klik ganda saat form dikirim
    document.addEventListener('submit', (e) => {
        if (e.defaultPrevented) return;
        bekerja = true;
        e.target.querySelectorAll('button').forEach(b => b.disabled = true);
    });

    // Perbarui daftar dan nomor yang dilayani tiap 5 detik
    async function segarkan() {
    if (bekerja) return;
    try {
        const r = await fetch(location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
        if (r.redirected && r.url.includes('/login')) { location.reload(); return; }
        if (!r.ok || document.hidden) return;   // sinyal tetap terkirim, tampilan tidak diganti saat tersembunyi
        const doc = new DOMParser().parseFromString(await r.text(), 'text/html');
        const baru = doc.getElementById('portal');
        if (baru) { portal.innerHTML = baru.innerHTML; lucide.createIcons(); }
    } catch (e) { /* coba lagi 5 detik kemudian */ }
}
    setInterval(segarkan, 5000);

    // Penghitung durasi layanan
    setInterval(() => {
        const el = document.getElementById('durasi');
        if (!el) return;
        const s = Math.max(0, Math.floor(Date.now() / 1000) - Number(el.dataset.mulai));
        el.textContent = String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
    }, 1000);
</script>
</body>
</html>