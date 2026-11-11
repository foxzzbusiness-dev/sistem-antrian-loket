<header class="bg-white border-b border-garis">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center gap-8">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-navy text-white grid place-items-center">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
            </span>
            <span class="leading-tight">
                <span class="block text-sm font-bold text-slate-900">Sistem Antrian Loket</span>
                <span class="block text-[11px] text-slate-500">Mal Pelayanan Publik • Kota Bandung</span>
            </span>
        </a>

        <nav class="hidden md:flex items-center gap-8 mx-auto text-sm">
            @foreach ([['beranda', 'Beranda'], ['layanan', 'Layanan'], ['panduan', 'Panduan antrean']] as [$rute, $label])
                <a href="{{ route($rute) }}"
                    class="{{ request()->routeIs($rute) ? 'font-bold text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="ml-auto md:ml-0 flex items-center gap-4">
            <span
                class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-1 text-[11px] font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Pelayanan dibuka
            </span>
            <button aria-label="Notifikasi" class="text-slate-500 hover:text-slate-900">
                <i data-lucide="bell" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</header>