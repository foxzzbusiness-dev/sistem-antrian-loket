@php
    \Carbon\Carbon::setLocale('id');
    $inisial = collect(explode(' ', auth()->user()->name))->map(fn($k) => mb_substr($k, 0, 1))->take(2)->join('');
    $jmlMenunggu = \App\Models\Antrean::whereDate('diterbitkan_at', today())->where('status', 'menunggu')->count();
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Dasbor Petugas') • Sistem Antrian Loket</title>
    @include('partials.aset')
</head>

<body class="bg-kanvas font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="hidden lg:flex w-64 shrink-0 flex-col bg-white border-r border-garis sticky top-0 h-screen p-4">
            <div class="flex items-center gap-3 px-2 py-1">
                <span class="w-9 h-9 rounded-lg bg-navy text-white grid place-items-center"><i
                        data-lucide="layout-dashboard" class="w-4 h-4"></i></span>
                <span class="leading-tight">
                    <span class="block text-sm font-bold text-slate-900">Sistem Antrian</span>
                    <span class="block text-[11px] text-slate-500">Loket • Portal petugas</span>
                </span>
            </div>

            <nav class="mt-6 text-xs overflow-y-auto">
                <div class="px-2 mb-2 text-[10px] font-semibold tracking-wide text-slate-400">MENU UTAMA</div>
                @foreach ([
                        ['layout-dashboard', 'Dashboard', route('petugas.dashboard'), true, null],
                    ] as [$ikon, $label, $url, $aktif, $badge])
                    <a href="{{ $url }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 mb-0.5 {{ $aktif ? 'bg-navy-50 text-navy font-semibold' : 'text-slate-500 hover:bg-kanvas hover:text-slate-900' }}">
                        <i data-lucide="{{ $ikon }}" class="w-4 h-4"></i>
                        <span class="flex-1">{{ $label }}</span>
                        @if ($badge)<span
                        class="text-[10px] font-semibold bg-navy-50 text-navy rounded-full px-1.5 py-0.5">{{ $badge }}</span>@endif
                    </a>
                @endforeach

                <div class="px-2 mt-6 mb-2 text-[10px] font-semibold tracking-wide text-slate-400">SISTEM</div>
                @foreach ([['settings', 'Pengaturan'], ['circle-help', 'Pusat bantuan']] as [$ikon, $label])
                    <a href="#"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 mb-0.5 text-slate-500 hover:bg-kanvas hover:text-slate-900">
                        <i data-lucide="{{ $ikon }}" class="w-4 h-4"></i>{{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto">
               
                <form method="POST" action="{{ route('petugas.keluar') }}" class="mt-3">
                    @csrf
                    <button
                        class="w-full flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs text-slate-500 hover:bg-kanvas hover:text-slate-900">
                        <i data-lucide="log-out" class="w-4 h-4"></i>Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Konten --}}
        <div class="flex-1 min-w-0 flex flex-col">
            <header class="bg-white border-b border-garis h-16 px-6 flex items-center justify-between">
                <div class="leading-tight">
                    <div class="text-sm font-bold text-slate-900">@yield('judul_halaman', 'Dasbor petugas')</div>
                    <div class="text-[11px] text-slate-500">{{ now()->translatedFormat('l, j F Y') }}</div>
                </div>
                <div class="flex items-center gap-4">
                    <button aria-label="Notifikasi" class="text-slate-500 hover:text-slate-900"><i data-lucide="bell"
                            class="w-4 h-4"></i></button>
                    <span class="h-6 w-px bg-garis"></span>
                    <div class="flex items-center gap-2.5">
                        <span
                            class="w-8 h-8 rounded-full bg-navy-50 text-navy text-[11px] font-bold grid place-items-center">{{ $inisial }}</span>
                        <span class="hidden sm:block leading-tight">
                            <span class="block text-xs font-semibold">{{ auth()->user()->name }}</span>
                            <span class="block text-[11px] text-slate-500">
                                {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Petugas • ' . auth()->user()->loket }}
                            </span>
                        </span>
                    </div>
                    <form method="POST" action="{{ route('petugas.keluar') }}" class="lg:hidden">
                        @csrf
                        <button class="text-xs font-semibold text-navy">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="p-6 flex-1">
                @if (session('sukses'))
                    <div class="mb-5 rounded-lg bg-emerald-50 text-emerald-800 text-sm px-4 py-3 border border-emerald-200">
                        {{ session('sukses') }}</div>
                @endif
                @if (session('galat'))
                    <div class="mb-5 rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-200">
                        {{ session('galat') }}</div>
                @endif
                @yield('isi')
            </main>
        </div>
    </div>
    <script>lucide.createIcons();</script>
    @stack('skrip')
</body>

</html>