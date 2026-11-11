<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Sistem Antrian Loket')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Untuk produksi, pindahkan Tailwind ke Vite (npm). CDN ini cukup untuk pengembangan. --}}
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
            colors: { navy: { DEFAULT: '#14365a', 700: '#1b4674', 50: '#e8f0fa' }, kanvas: '#f4f7fa', garis: '#e3e9f0' }
        } } };
    </script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
</head>
<body class="bg-kanvas font-sans text-slate-800 antialiased min-h-screen flex flex-col">

<header class="bg-white border-b border-garis">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center gap-8">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-navy text-white grid place-items-center"><i data-lucide="layout-dashboard" class="w-4 h-4"></i></span>
            <span class="leading-tight">
                <span class="block text-sm font-bold text-slate-900">Sistem Antrian Loket</span>
                <span class="block text-[11px] text-slate-500">Mal Pelayanan Publik • Kota Bandung</span>
            </span>
        </a>

        <nav class="hidden md:flex items-center gap-8 mx-auto text-sm">
            @foreach ([['beranda','Beranda'],['layanan','Layanan'],['panduan','Panduan antrean']] as [$rute,$label])
                <a href="{{ route($rute) }}"
                   class="{{ request()->routeIs($rute) ? 'font-bold text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">{{ $label }}</a>
            @endforeach
        </nav>
                <div class="ml-auto md:ml-0 flex items-center gap-4">
            <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-1 text-[11px] font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Pelayanan dibuka
            </span>
            <button aria-label="Notifikasi" class="text-slate-500 hover:text-slate-900"><i data-lucide="bell" class="w-4 h-4"></i></button>
        </div>
    </div>
</header>

<main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">
    @if (session('sukses'))
        <div class="mb-5 rounded-lg bg-emerald-50 text-emerald-800 text-sm px-4 py-3 border border-emerald-200">{{ session('sukses') }}</div>
    @endif
    @if (session('galat') || $errors->any())
        <div class="mb-5 rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-200">{{ session('galat') ?? $errors->first() }}</div>
    @endif
    @yield('isi')
</main>

<footer class="max-w-7xl w-full mx-auto px-6 py-5 flex justify-between text-[11px] text-slate-500">
    <span>© {{ date('Y') }} Sistem Antrian Loket. Mal Pelayanan Publik Kota Bandung.</span>
    <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Sistem terhubung</span>
</footer>

<script>lucide.createIcons();</script>
</body>
</html>