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
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: { navy: { DEFAULT: '#14365a', 700: '#1b4674', 50: '#e8f0fa' }, kanvas: '#f4f7fa', garis: '#e3e9f0' }
                }
            }
        };
    </script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
</head>

<body class="bg-kanvas font-sans text-slate-800 antialiased min-h-screen flex flex-col">

    {{-- @include('components.header') --}}

    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">
        @include('components.alert')
        @yield('isi')
    </main>

    {{-- @include('components.footer') --}}

    <script>lucide.createIcons();</script>
</body>

</html>