<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Petugas • Sistem Antrian Loket</title>
    @include('partials.aset')
</head>
<body class="font-sans antialiased text-slate-800">
<div class="min-h-screen grid lg:grid-cols-[46%_1fr]">

    {{-- Panel kiri --}}
    <aside class="hidden lg:flex flex-col bg-navy text-white p-10 xl:p-14">
        <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-white text-navy grid place-items-center"><i data-lucide="layout-dashboard" class="w-4 h-4"></i></span>
            <span class="leading-tight">
                <span class="block text-sm font-bold">Sistem Antrian Loket</span>
                <span class="block text-[11px] text-white/60">Mal Pelayanan Publik • Kota Bandung</span>
            </span>
        </div>

        <div class="mt-16">
            <span class="inline-flex items-center gap-1.5 rounded-md bg-white/10 px-2.5 py-1 text-[10px] font-semibold tracking-wide">
                <i data-lucide="shield-check" class="w-3 h-3"></i>PORTAL PETUGAS
            </span>
            <h1 class="mt-5 text-4xl xl:text-5xl font-extrabold leading-tight tracking-tight">SISTEM ANTRIAN LOKET</h1>
            <p class="mt-4 text-sm text-white/70 max-w-xs">Layanan antrean digital yang mudah, cepat, dan nyaman.</p>

            <div class="mt-10 rounded-2xl bg-white/10 p-5 max-w-md">
                <div class="flex items-center justify-between text-[11px] text-white/80">
                    <span>Pelayanan yang lebih tertata</span>
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                </div>
                <div class="mt-3 rounded-xl bg-white text-navy p-4 flex items-center justify-between">
                    <div>
                        <div class="text-[9px] tracking-wide text-slate-400">NOMOR ANTREAN</div>
                        <div class="text-4xl font-extrabold tracking-tight">A-025</div>
                    </div>
                    <div class="border-l border-garis pl-5 text-center text-[11px] font-medium">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 mx-auto mb-1 text-slate-400"></i>Loket 01
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2 text-[11px] text-white/70">
                    <i data-lucide="circle-check" class="w-3.5 h-3.5"></i>Antrean teratur. Pelayanan lebih optimal.
                </div>
            </div>
        </div>

        <div class="mt-auto">
            <div class="flex justify-between border-t border-white/15 pt-4 text-[11px] text-white/70">
                <span>Transparan</span><span>Terintegrasi</span><span>Mudah diakses</span>
            </div>
            <div class="mt-10 text-[10px] tracking-wide text-white/50">MAL PELAYANAN PUBLIK KOTA BANDUNG</div>
        </div>
    </aside>

    {{-- Panel kanan --}}
    <main class="flex flex-col bg-white px-6 sm:px-10 py-6">
        <div class="flex items-center justify-between">
            <span class="lg:hidden flex items-center gap-2 text-sm font-bold text-navy">
                <span class="w-8 h-8 rounded-lg bg-navy text-white grid place-items-center"><i data-lucide="layout-dashboard" class="w-4 h-4"></i></span>Sistem Antrian Loket
            </span>
            <a href="#" class="ml-auto inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800">
                <i data-lucide="circle-help" class="w-3.5 h-3.5"></i>Pusat bantuan
            </a>
        </div>

        <div class="flex-1 grid place-items-center py-10">
            <div class="w-full max-w-sm">
                <span class="w-10 h-10 rounded-lg bg-navy-50 text-navy grid place-items-center"><i data-lucide="log-in" class="w-4 h-4"></i></span>
                <h2 class="mt-4 text-2xl font-bold text-navy">Selamat datang kembali</h2>
                <p class="mt-1.5 text-xs text-slate-500">Masuk ke akun petugas untuk mengelola antrean dan memberikan pelayanan terbaik.</p>

                @if ($errors->any())
                    <div class="mt-5 rounded-lg bg-red-50 border border-red-200 text-red-800 text-xs px-3.5 py-2.5">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('petugas.masuk') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="username" class="text-xs font-semibold text-slate-800">Nama pengguna</label>
                        <div class="relative mt-1.5">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" autofocus required
                                   placeholder="Masukkan nama pengguna"
                                   class="w-full rounded-lg border border-garis pl-10 pr-3 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/20">
                        </div>
                    </div>

                    <div>
                        <label for="sandi" class="text-xs font-semibold text-slate-800">Kata sandi</label>
                        <div class="relative mt-1.5">
                            <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="sandi" name="password" type="password" autocomplete="current-password" required
                                   placeholder="Masukkan kata sandi"
                                   class="w-full rounded-lg border border-garis pl-10 pr-10 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/20">
                            <button type="button" id="lihat" aria-label="Tampilkan kata sandi" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                                <span id="s-lihat"><i data-lucide="eye" class="w-4 h-4"></i></span>
                                <span id="s-sembunyi" class="hidden"><i data-lucide="eye-off" class="w-4 h-4"></i></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="inline-flex items-center gap-2 text-slate-600">
                            <input type="checkbox" name="ingat" value="1" class="rounded border-garis accent-[#14365a]" @checked(old('ingat'))>Ingat saya
                        </label>
                        <a href="#" class="font-semibold text-navy hover:underline">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-navy text-white text-sm font-semibold py-3 hover:bg-navy-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy">
                        <i data-lucide="log-in" class="w-4 h-4"></i>Login
                    </button>
                </form>

                <div class="mt-6 flex gap-2.5 rounded-lg bg-kanvas px-3.5 py-3 text-[11px] text-slate-500">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 shrink-0 mt-0.5"></i>
                    Akses khusus petugas berwenang. Jaga kerahasiaan akun Anda dan keluar setelah selesai bertugas.
                </div>
            </div>
        </div>

        <p class="text-center text-[11px] text-slate-400">© {{ date('Y') }} Sistem Antrian Loket. Seluruh hak dilindungi.</p>
    </main>
</div>

<script>
    lucide.createIcons();
    const sandi = document.getElementById('sandi');
    document.getElementById('lihat').addEventListener('click', () => {
        const tampil = sandi.type === 'password';
        sandi.type = tampil ? 'text' : 'password';
        document.getElementById('s-lihat').classList.toggle('hidden', tampil);
        document.getElementById('s-sembunyi').classList.toggle('hidden', !tampil);
    });
</script>
</body>
</html>