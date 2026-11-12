@extends('layouts.admin')
@section('judul', 'Kelola Petugas')
@section('judul_halaman', 'Kelola petugas')

@section('isi')
<div class="flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Kelola petugas</h1>
        <p class="text-xs text-slate-500 mt-1">Kelola akun, penugasan loket, dan status petugas dalam satu tempat.</p>
    </div>
    <button type="button" onclick="buka('tambah')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">
        <i data-lucide="plus" class="w-3.5 h-3.5"></i>Tambah petugas
    </button>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mt-5">
    @foreach ([['Total petugas', $stat['total']], ['Petugas aktif', $stat['aktif']], ['Sedang bertugas', $stat['bertugas']], ['Nonaktif', $stat['nonaktif']]] as [$label, $nilai])
        <div class="bg-white rounded-xl border border-garis px-5 py-4">
            <div class="text-[11px] text-slate-500">{{ $label }}</div>
            <div class="mt-1 text-3xl font-bold text-slate-900">{{ $nilai }}</div>
        </div>
    @endforeach
</div>

{{-- Daftar --}}
<section class="bg-white rounded-xl border border-garis mt-5">
    <div class="p-5">
        <h2 class="text-sm font-semibold text-slate-900">Daftar petugas</h2>
        <form method="GET" class="relative mt-4 max-w-md">
            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, ID, atau email petugas..."
                   class="w-full rounded-lg border border-garis pl-9 pr-3 py-2.5 text-xs focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/20">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-kanvas text-[11px] text-slate-500">
                <tr>
                    <th class="px-5 py-3 font-normal">Nama petugas</th>
                    <th class="px-3 py-3 font-normal">ID petugas</th>
                    <th class="px-3 py-3 font-normal">Penugasan</th>
                    <th class="px-3 py-3 font-normal">Shift</th>
                    <th class="px-3 py-3 font-normal">Status akun</th>
                    <th class="px-5 py-3 font-normal text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($daftar as $p)
                <tr class="border-t border-garis">
                    <td class="px-5 py-3">
                        <div class="font-semibold text-slate-900">{{ $p->name }}</div>
                        <div class="text-[10px] text-slate-500">{{ $p->email }}</div>
                    </td>
                    <td class="px-3 py-3 text-slate-600">{{ $p->kode }}</td>
                    <td class="px-3 py-3 text-slate-600">{{ $p->loket ?? 'Belum ditempatkan' }}</td>
                    <td class="px-3 py-3 text-slate-600">{{ str_replace(':', '.', substr($p->shift_mulai, 0, 5)) }}–{{ str_replace(':', '.', substr($p->shift_selesai, 0, 5)) }}</td>
                    <td class="px-3 py-3">
                        @if ($p->aktif)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-medium text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-medium text-slate-500"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3 text-slate-400">
                            <button type="button" title="Ubah data" aria-label="Ubah {{ $p->name }}" class="hover:text-navy"
                                    data-p="{{ json_encode(['id' => $p->id, 'name' => $p->name, 'username' => $p->username, 'email' => $p->email, 'loket' => $p->loket, 'shift_mulai' => substr($p->shift_mulai, 0, 5), 'shift_selesai' => substr($p->shift_selesai, 0, 5)]) }}"
                                    onclick="buka('ubah', JSON.parse(this.dataset.p))">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>

                            <form method="POST" action="{{ route('admin.petugas.status', $p) }}"
                                  onsubmit="return confirm('{{ $p->aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $p->name }}?')">@csrf
                                <button title="{{ $p->aktif ? 'Nonaktifkan akun' : 'Aktifkan akun' }}" aria-label="{{ $p->aktif ? 'Nonaktifkan' : 'Aktifkan' }} {{ $p->name }}" class="hover:text-amber-600">
                                    <i data-lucide="power" class="w-4 h-4"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.petugas.hapus', $p) }}"
                                  onsubmit="return confirm('Hapus akun {{ $p->name }}? Tindakan ini tidak bisa dibatalkan.')">@csrf @method('DELETE')
                                <button title="Hapus akun" aria-label="Hapus {{ $p->name }}" class="hover:text-red-600">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400">
                    {{ request('q') ? 'Tidak ada petugas yang cocok dengan pencarian.' : 'Belum ada petugas. Tekan "Tambah petugas" untuk membuat akun pertama.' }}
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-garis px-5 py-3 text-[11px] text-slate-400">
        <span>Menampilkan {{ $daftar->firstItem() ?? 0 }}–{{ $daftar->lastItem() ?? 0 }} dari {{ $daftar->total() }} petugas</span>
        <div>{{ $daftar->links() }}</div>
    </div>
</section>

{{-- Modal tambah / ubah --}}
<dialog id="modal" class="w-full max-w-md rounded-2xl p-0 backdrop:bg-black/40">
    <form method="POST" id="form-petugas" class="p-6">
        @csrf
        <input type="hidden" name="_method" id="metode" value="PUT" disabled>
        <input type="hidden" name="_mode" id="mode">
        <input type="hidden" name="_id" id="id-petugas">

        <h2 id="judul-modal" class="text-base font-bold text-slate-900">Tambah petugas</h2>

        @if ($errors->any())
            <div class="mt-3 rounded-lg bg-red-50 border border-red-200 px-3.5 py-2.5 text-xs text-red-800">
                <ul class="list-disc pl-4 space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="mt-4 space-y-3.5 text-xs">
            @foreach ([['name', 'Nama lengkap', 'text'], ['username', 'Nama pengguna', 'text'], ['email', 'Email', 'email']] as [$n, $label, $tipe])
                <div>
                    <label for="f-{{ $n }}" class="font-semibold text-slate-800">{{ $label }}</label>
                    <input id="f-{{ $n }}" name="{{ $n }}" type="{{ $tipe }}" required
                           class="mt-1.5 w-full rounded-lg border border-garis px-3 py-2.5 focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/20">
                </div>
            @endforeach

            <div>
                <label for="f-password" class="font-semibold text-slate-800">Kata sandi</label>
                <input id="f-password" name="password" type="password" autocomplete="new-password" minlength="8"
                       class="mt-1.5 w-full rounded-lg border border-garis px-3 py-2.5 focus:outline-none focus:border-navy focus:ring-2 focus:ring-navy/20">
                <p id="bantu-sandi" class="mt-1 text-[10px] text-slate-400">Minimal 8 karakter.</p>
            </div>

            <div>
                <label for="f-loket" class="font-semibold text-slate-800">Penugasan loket</label>
                <select id="f-loket" name="loket" class="mt-1.5 w-full rounded-lg border border-garis px-3 py-2.5 focus:outline-none focus:border-navy">
                    <option value="">Belum ditempatkan</option>
                    @foreach ($daftarLoket as $nama)<option value="{{ $nama }}">{{ $nama }}</option>@endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="f-shift_mulai" class="font-semibold text-slate-800">Shift mulai</label>
                    <input id="f-shift_mulai" name="shift_mulai" type="time" required value="08:00"
                           class="mt-1.5 w-full rounded-lg border border-garis px-3 py-2.5 focus:outline-none focus:border-navy">
                </div>
                <div>
                    <label for="f-shift_selesai" class="font-semibold text-slate-800">Shift selesai</label>
                    <input id="f-shift_selesai" name="shift_selesai" type="time" required value="16:00"
                           class="mt-1.5 w-full rounded-lg border border-garis px-3 py-2.5 focus:outline-none focus:border-navy">
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="modal.close()" class="rounded-lg border border-garis px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-kanvas">Batal</button>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</dialog>
@endsection

@push('skrip')
<script>
    const modal = document.getElementById('modal');
    const form = document.getElementById('form-petugas');
    const urlSimpan = @json(route('admin.petugas.simpan'));
    const urlUbah = @json(route('admin.petugas.ubah', '__ID__'));
    const KOLOM = ['name', 'username', 'email', 'loket', 'shift_mulai', 'shift_selesai'];

    function buka(mode, d = {}) {
        const ubah = mode === 'ubah';
        form.action = ubah ? urlUbah.replace('__ID__', d.id) : urlSimpan;
        document.getElementById('metode').disabled = !ubah;
        document.getElementById('mode').value = mode;
        document.getElementById('id-petugas').value = d.id ?? '';
        document.getElementById('judul-modal').textContent = ubah ? 'Ubah data petugas' : 'Tambah petugas';

        const sandi = document.getElementById('f-password');
        sandi.required = !ubah;
        sandi.value = '';
        document.getElementById('bantu-sandi').textContent = ubah ? 'Kosongkan jika kata sandi tidak diubah.' : 'Minimal 8 karakter.';

        KOLOM.forEach(k => {
            const el = document.getElementById('f-' + k);
            if (d[k] !== undefined && d[k] !== null) el.value = d[k];
            else el.value = (k === 'shift_mulai') ? '08:00' : (k === 'shift_selesai') ? '16:00' : '';
        });

        modal.showModal();
    }

    // Buka kembali modal bila validasi gagal, lengkap dengan isian sebelumnya
    const lama = @json(old());
    if (lama && lama._mode) buka(lama._mode, lama);
</script>
@endpush