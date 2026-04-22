<x-layouts.app title="Riwayat Pasien">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Riwayat Pasien
            </h2>
            <p class="text-sm text-slate-500">
                Daftar pasien yang sudah diperiksa
            </p>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">

                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Nama Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4">Catatan</th>
                            <th class="px-6 py-4">Biaya</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-slate-700">
                        @forelse($riwayatPasien as $item)
                            <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">
                                        {{ $item->daftarPoli->pasien->nama ?? '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        No. RM: {{ $item->daftarPoli->pasien->no_rm ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item->daftarPoli->keluhan ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($item->tgl_periksa)->format('d M Y, H:i') }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="line-clamp-2">
                                        {{ $item->catatan ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    Rp {{ number_format($item->biaya_periksa, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('riwayat-pasien.detail', $item->id) }}"
                                       class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                                        <i class="fas fa-eye text-xs"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                    Belum ada riwayat pasien
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</x-layouts.app>