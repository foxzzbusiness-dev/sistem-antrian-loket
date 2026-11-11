@if (session('sukses'))
    <div class="mb-5 rounded-lg bg-emerald-50 text-emerald-800 text-sm px-4 py-3 border border-emerald-200">
        {{ session('sukses') }}
    </div>
@endif

@if (session('galat') || $errors->any())
    <div class="mb-5 rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-200">
        {{ session('galat') ?? $errors->first() }}
    </div>
@endif