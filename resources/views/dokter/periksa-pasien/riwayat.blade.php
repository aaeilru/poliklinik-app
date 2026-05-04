<x-layouts.app title="Riwayat Pasien">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Riwayat Pasien</h2>
        {{-- ── Fitur 5: Export Excel ── --}}
        <a href="{{ route('dokter.export.riwayat') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700
                  text-white text-sm font-semibold rounded-xl transition">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-4">Pasien</th>
                            <th class="px-5 py-4">No. Antrian</th>
                            <th class="px-5 py-4">Jadwal</th>
                            <th class="px-5 py-4">Tgl Periksa</th>
                            <th class="px-5 py-4">Obat</th>
                            <th class="px-5 py-4 text-right">Total</th>
                            <th class="px-5 py-4 text-center">Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($riwayat as $daftar)
                        @php
                            $periksa = $daftar->periksa;
                            $bukti   = $periksa?->buktiPembayaran;
                        @endphp
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $daftar->pasien->nama }}</td>
                            <td class="px-5 py-4 text-center font-bold text-indigo-600">{{ $daftar->no_antrian }}</td>
                            <td class="px-5 py-4">
                                {{ $daftar->jadwalPeriksa->hari }},
                                {{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_mulai)->format('H:i') }}
                            </td>
                            <td class="px-5 py-4 text-slate-500">
                                {{ $periksa ? \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-slate-500">
                                @if($periksa && $periksa->detailPeriksas->isNotEmpty())
                                    {{ $periksa->detailPeriksas->map(fn($d) => $d->obat->nama_obat)->implode(', ') }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-800">
                                Rp {{ $periksa ? number_format($periksa->total_biaya, 0, ',', '.') : '0' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($bukti && $bukti->isVerified())
                                    <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Lunas</span>
                                @elseif($bukti && $bukti->file_bukti)
                                    <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Menunggu</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-600 text-xs font-bold">Belum</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-14 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                Belum ada pasien yang diperiksa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>
