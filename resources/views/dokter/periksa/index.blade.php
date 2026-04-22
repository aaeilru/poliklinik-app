<x-layouts.app title="Periksa Pasien">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Antrian Pasien</h2>
    </div>

    @if(session('message'))
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold border
        {{ session('type') === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' }}">
        {{ session('message') }}
    </div>
    @endif

    {{-- Alert jika ada obat habis --}}
    @if($obatHabis > 0)
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold border bg-amber-50 text-amber-700 border-amber-200">
        <i class="fas fa-triangle-exclamation mr-2"></i>
        {{ $obatHabis }} obat sedang habis stok. Periksa halaman
        <a href="{{ route('obat.index') }}" class="underline font-bold">Manajemen Obat</a> untuk mengisi ulang.
    </div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-4 text-center">No. Antrian</th>
                            <th class="px-5 py-4">Nama Pasien</th>
                            <th class="px-5 py-4">Keluhan</th>
                            <th class="px-5 py-4">Jadwal</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($daftarPasien as $daftar)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full
                                             bg-indigo-100 text-indigo-700 font-black text-lg">
                                    {{ $daftar->no_antrian }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $daftar->pasien->nama }}</td>
                            <td class="px-5 py-4 text-slate-500 max-w-xs truncate">{{ $daftar->keluhan ?? '-' }}</td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $daftar->jadwalPeriksa->hari }},
                                {{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('periksa.create', $daftar->id) }}"
                                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg
                                          bg-primary hover:bg-primary/90 text-white text-xs font-semibold transition">
                                    <i class="fas fa-stethoscope"></i> Periksa
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-14 text-slate-400">
                                <i class="fas fa-check-circle text-3xl mb-2 block text-green-400"></i>
                                Tidak ada pasien dalam antrian
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>
