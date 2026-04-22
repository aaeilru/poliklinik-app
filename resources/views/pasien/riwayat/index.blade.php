<x-layouts.app title="Riwayat Pendaftaran Poli">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Riwayat Pendaftaran Poli</h2>
    </div>

    {{-- Flash message --}}
    @if(session('message'))
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold border
        {{ session('type') === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' }}">
        {{ session('message') }}
    </div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-4">No</th>
                            <th class="px-5 py-4">Poli</th>
                            <th class="px-5 py-4">Dokter</th>
                            <th class="px-5 py-4">Jadwal</th>
                            <th class="px-5 py-4 text-center">No. Antrian</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($riwayat as $i => $daftar)
                        @php
                            $sudahDiperiksa = $daftar->sudahDiperiksa();
                        @endphp
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                            <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>

                            <td class="px-5 py-3 font-semibold text-slate-800">
                                {{ $daftar->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                            </td>

                            <td class="px-5 py-3">
                                {{ $daftar->jadwalPeriksa->dokter->nama ?? '-' }}
                            </td>

                            <td class="px-5 py-3">
                                {{ $daftar->jadwalPeriksa->hari }},
                                {{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_selesai)->format('H:i') }}
                                <p class="text-xs text-slate-400">
                                    Daftar: {{ $daftar->created_at->format('d M Y') }}
                                </p>
                            </td>

                            <td class="px-5 py-3 text-center">
                                <span class="inline-block w-8 h-8 rounded-full bg-indigo-100 text-indigo-700
                                             font-bold text-sm flex items-center justify-center mx-auto">
                                    {{ $daftar->no_antrian }}
                                </span>
                            </td>

                            <td class="px-5 py-3 text-center">
                                @if($sudahDiperiksa)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold
                                                 rounded-full bg-green-100 text-green-600">
                                        <i class="fas fa-check"></i> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold
                                                 rounded-full bg-amber-100 text-amber-600">
                                        <i class="fas fa-clock"></i> Menunggu
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3 text-right">
                                @if($sudahDiperiksa)
                                    {{-- Tombol detail hanya muncul jika sudah diperiksa --}}
                                    <a href="{{ route('pasien.riwayat.show', $daftar->id) }}"
                                       class="inline-flex items-center gap-1 px-4 py-2 rounded-lg
                                              bg-primary hover:bg-primary/90 text-white text-xs font-semibold transition">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-14 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                Belum ada riwayat pendaftaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>
