@extends('layouts.pengguna')
@section('judul', 'Ambil Tiket • Sistem Antrian Loket')

@section('isi')
<h1 class="sr-only">Pilih layanan dan ambil tiket antrean</h1>

@if ($daftarLayanan->isEmpty())
    <div class="rounded-3xl bg-white border border-garis px-6 py-16 text-center">
        <p class="text-sm font-semibold text-slate-800">Layanan belum tersedia</p>
        <p class="mt-1 text-xs text-slate-500">Silakan hubungi petugas di loket informasi.</p>
    </div>
@else
    <div class="grid md:grid-cols-3 gap-5">
        @foreach ($daftarLayanan as $l)
            <section class="rounded-3xl bg-[#eef2f7] p-6 flex flex-col min-h-[220px]">
                <h2 class="text-base font-bold text-slate-900">{{ $l->nama }}</h2>
                <p class="mt-3 text-sm text-slate-600">{{ $l->deskripsi }}</p>

                <form method="POST" action="{{ route('antrean.ambil', $l) }}" class="mt-auto pt-8"
                      onsubmit="this.querySelector('button').disabled = true">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-full bg-navy text-white text-sm font-bold py-3.5 hover:bg-navy-700 disabled:opacity-60 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy">
                        Ambil Tiket
                    </button>
                </form>
            </section>
        @endforeach
    </div>
@endif
@endsection