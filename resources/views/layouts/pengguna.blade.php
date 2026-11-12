<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Sistem Antrian Loket')</title>
    @include('partials.aset')
</head>
<body class="bg-kanvas font-sans text-slate-800 antialiased min-h-screen flex flex-col">

<header class="bg-white border-b border-garis">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-navy text-white grid place-items-center"><i data-lucide="layout-dashboard" class="w-4 h-4"></i></span>
            <span class="leading-tight">
                <span class="block text-sm font-bold text-slate-900">Sistem Antrian Loket</span>
                <span class="block text-[11px] text-slate-500">Mal Pelayanan Publik • Kota Bandung</span>
            </span>
        </a>
    </div>
</header>

<main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
    @if (session('galat'))
        <div class="mb-5 rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-200">{{ session('galat') }}</div>
    @endif
    @yield('isi')
</main>

<script>lucide.createIcons();</script>
</body>
</html>