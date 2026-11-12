@extends('layouts.pengguna')
@section('judul', 'Layanan • Sistem Antrian Loket')

@section('isi')


<h1 class="text-2xl font-bold text-slate-900">Layanan</h1>
<p class="text-sm text-slate-500 mt-1">Pilih layanan sesuai keperluan Anda, lalu ambil nomor antrean dari beranda.</p>

<div class="grid md:grid-cols-3 gap-5 mt-6">
    @foreach ($daftarLayanan as $l)
        <article class="bg-white rounded-2xl border border-garis p-6 flex flex-col">
            <span class="w-10 h-10 rounded-lg bg-navy-50 text-navy grid place-items-center"><i data-lucide="{{ $l->ikon }}" class="w-5 h-5"></i></span>
            <h2 class="mt-4 font-semibold text-slate-900">{{ $l->nama }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $l->deskripsi }}</p>
            <dl class="mt-4 text-xs space-y-1.5 text-slate-600">
                <div class="flex justify-between"><dt>Loket</dt><dd class="font-medium text-slate-900">{{ $l->loket }}</dd></div>
                <div class="flex justify-between"><dt>Rata-rata layanan</dt><dd class="font-medium text-slate-900">{{ $l->rata_rata_menit }} menit</dd></div>
                <div class="flex justify-between"><dt>Menunggu saat ini</dt><dd class="font-medium text-slate-900">{{ $l->antrean()->where('status','menunggu')->count() }} orang</dd></div>
            </dl>
            <button type="submit" class="mt-5 text-center rounded-lg bg-navy text-white text-sm font-semibold px-4 py-2.5 hover:bg-navy-700">Pilih layanan ini</a>
        </article>
    @endforeach
</div>
@endsection