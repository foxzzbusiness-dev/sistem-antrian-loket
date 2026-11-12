<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket {{ $antrean->nomor }}</title>
    @include('partials.aset')
    <style>
        @page { size: 80mm auto; margin: 0; }
        @media print {
            body { background: #fff !important; }
            .tanpa-cetak { display: none !important; }
            .kertas { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-kanvas font-sans text-slate-900 antialiased min-h-screen grid place-items-center p-6">

<div>
    {{-- Tiket (bagian inilah yang tercetak) --}}
    <article class="kertas w-[80mm] bg-white p-5 text-center shadow-lg rounded-xl">
        <div class="text-sm font-bold">Sistem Antrian Loket</div>
        <div class="text-[11px] text-slate-500">Mal Pelayanan Publik • Kota Bandung</div>

        <div class="my-4 border-t border-dashed border-slate-400"></div>

        <div class="text-[11px] text-slate-500">Nomor antrean Anda</div>
        <div class="text-6xl font-extrabold tracking-tight my-1">{{ $antrean->nomor }}</div>
        <div class="text-sm font-semibold">{{ $antrean->layanan->nama }}</div>

        <div class="my-4 border-t border-dashed border-slate-400"></div>

        <dl class="text-xs space-y-1.5 text-left">
            <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd class="font-medium">{{ $antrean->diterbitkan_at->translatedFormat('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jam</dt><dd class="font-medium">{{ $antrean->diterbitkan_at->format('H.i') }} WIB</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Antrean di depan</dt><dd class="font-medium">{{ $menunggu }} orang</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Estimasi tunggu</dt><dd class="font-medium">± {{ $menunggu * $antrean->layanan->rata_rata_menit }} menit</dd></div>
        </dl>

        <div class="my-4 border-t border-dashed border-slate-400"></div>

        <p class="text-[11px] text-slate-600">Silakan menunggu hingga nomor Anda dipanggil. Simpan tiket ini sampai selesai dilayani.</p>
    </article>

    {{-- Tombol (tidak ikut tercetak) --}}
    <div class="tanpa-cetak mt-5 w-[80mm] max-w-full mx-auto text-center">

        {{-- Keadaan awal: belum dicetak --}}
        <div id="aksi-awal" class="flex gap-3">
            <a href="{{ route('beranda') }}" class="flex-1 rounded-full border border-garis bg-white py-3 text-sm font-semibold text-slate-700 hover:bg-kanvas">Kembali</a>
            <button type="button" id="btn-cetak" class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-navy py-3 text-sm font-semibold text-white hover:bg-navy-700">
                <i data-lucide="printer" class="w-4 h-4"></i>Cetak
            </button>
        </div>

        {{-- Setelah dialog cetak ditutup: tanya apakah berhasil --}}
        <div id="aksi-konfirmasi" class="hidden">
            <p class="text-sm font-semibold text-slate-800">Apakah tiket sudah keluar dari printer?</p>
            <div class="mt-3 flex gap-3">
                <button type="button" id="btn-ulang" class="flex-1 inline-flex items-center justify-center gap-2 rounded-full border border-garis bg-white py-3 text-sm font-semibold text-slate-700 hover:bg-kanvas">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>Cetak ulang
                </button>
                <a href="{{ route('beranda') }}" class="flex-1 rounded-full bg-navy py-3 text-sm font-semibold text-white hover:bg-navy-700">Sudah, selesai</a>
            </div>
            <p class="mt-3 text-[11px] text-slate-500">Tiket tidak keluar? Tekan Cetak ulang, atau minta bantuan petugas. Nomor antrean Anda tetap {{ $antrean->nomor }}.</p>
        </div>

        <p class="mt-4 text-[11px] text-slate-400">Kembali otomatis dalam <span id="hitung">60</span> detik</p>
    </div>
</div>

<script>
    lucide.createIcons();

    const urlBeranda = @json(route('beranda'));
    const BATAS_DETIK = 60;
    let sisa = BATAS_DETIK;

    const awal = document.getElementById('aksi-awal');
    const konfirmasi = document.getElementById('aksi-konfirmasi');
    const hitung = document.getElementById('hitung');

    function cetak() { window.print(); }

    document.getElementById('btn-cetak').addEventListener('click', cetak);
    document.getElementById('btn-ulang').addEventListener('click', cetak);

    // Dialog cetak ditutup (berhasil, gagal, atau dibatalkan) → tanyakan ke pengunjung
    window.addEventListener('afterprint', () => {
        awal.classList.add('hidden');
        konfirmasi.classList.remove('hidden');
        sisa = BATAS_DETIK;
    });

    // Kembali otomatis ke daftar layanan kalau layar dibiarkan
    ['click', 'touchstart', 'keydown'].forEach(e =>
        document.addEventListener(e, () => { sisa = BATAS_DETIK; }, { passive: true })
    );

    setInterval(() => {
        sisa--;
        hitung.textContent = Math.max(sisa, 0);
        if (sisa <= 0) window.location.href = urlBeranda;
    }, 1000);
</script>
</body>
</html>