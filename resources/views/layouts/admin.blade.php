@php
    \Carbon\Carbon::setLocale('id');
    $menu = [
        ['layout-dashboard', 'Ringkasan', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
        ['users', 'Kelola petugas', route('admin.petugas'), request()->routeIs('admin.petugas*')],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Dashboard Admin') • Portal Loket</title>
    @include('partials.aset')
</head>
<body class="bg-kanvas font-sans text-slate-800 antialiased">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="hidden lg:flex w-56 shrink-0 flex-col bg-navy text-white sticky top-0 h-screen px-4 py-6">
        <div class="px-2">
            <div class="text-xl font-bold leading-tight">Portal Loket</div>
            <div class="mt-0.5 text-[9px] tracking-widest text-white/50">ADMINISTRASI</div>
        </div>

        <div class="mt-8 px-2 text-[9px] tracking-widest text-white/50">MENU UTAMA</div>
        <nav class="mt-3 space-y-2">
            @foreach ($menu as [$ikon, $label, $url, $aktif])
                <a href="{{ $url }}"
                   class="flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs font-semibold {{ $aktif ? 'bg-blue-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i data-lucide="{{ $ikon }}" class="w-3.5 h-3.5"></i>{{ $label }}
                </a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('keluar') }}" class="mt-auto">
            @csrf
            <button class="w-full flex items-center gap-2.5 rounded-lg px-3.5 py-2.5 text-xs text-white/70 hover:bg-white/10 hover:text-white">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>Keluar
            </button>
        </form>
    </aside>

    <div class="flex-1 min-w-0 flex flex-col">
        <header class="bg-white border-b border-garis h-16 px-6 flex items-center justify-between">
            <div class="flex items-center gap-2 text-[11px]">
                <span class="text-slate-400">Administrasi</span>
                <span class="text-slate-300">/</span>
                <span class="font-semibold text-slate-900">@yield('judul_halaman', 'Ringkasan')</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right leading-tight">
                    <div class="text-xs font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-slate-500">Admin utama</div>
                </div>
                <form method="POST" action="{{ route('keluar') }}" class="lg:hidden">@csrf
                    <button class="text-xs font-semibold text-navy">Keluar</button>
                </form>
            </div>
        </header>

        {{-- Navigasi untuk layar kecil --}}
        <nav class="lg:hidden flex gap-2 bg-white border-b border-garis px-6 py-2">
            @foreach ($menu as [$ikon, $label, $url, $aktif])
                <a href="{{ $url }}" class="rounded-full px-3.5 py-1.5 text-xs font-semibold {{ $aktif ? 'bg-navy text-white' : 'bg-kanvas text-slate-600' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <main class="p-6 flex-1">
            @if (session('sukses'))
                <div class="mb-5 rounded-lg bg-emerald-50 text-emerald-800 text-sm px-4 py-3 border border-emerald-200">{{ session('sukses') }}</div>
            @endif
            @if (session('galat'))
                <div class="mb-5 rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-200">{{ session('galat') }}</div>
            @endif
            @yield('isi')
        </main>
    </div>
</div>
<script>lucide.createIcons();</script>
@stack('skrip')
</body>
</html>