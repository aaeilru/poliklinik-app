<x-layouts.app title="Detail Riwayat Pemeriksaan">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Detail Pemeriksaan</h2>
        <p class="text-sm text-slate-500">Informasi hasil pemeriksaan pasien</p>
    </div>

    <div class="grid gap-6">
        <div class="card bg-base-100 shadow-md border rounded-2xl">
            <div class="card-body">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Informasi Pendaftaran</h3>

                <div class="grid md:grid-cols-2 gap-4 text-sm text-slate-700">
                    <div>
                        <p class="font-semibold">Poli</p>
                        <p>{{ $daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Dokter</p>
                        <p>{{ $daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Hari</p>
                        <p>{{ $daftarPoli->jadwalPeriksa->hari ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Jam Periksa</p>
                        <p>{{ $daftarPoli->jadwalPeriksa->jam_mulai ?? '-' }} - {{ $daftarPoli->jadwalPeriksa->jam_selesai ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Nomor Antrian</p>
                        <p>{{ $daftarPoli->no_antrian }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Tanggal Periksa</p>
                        <p>{{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d-m-Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md border rounded-2xl">
            <div class="card-body">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Catatan Dokter</h3>
                <p class="text-sm text-slate-700">
                    {{ $periksa->catatan ?? '-' }}
                </p>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md border rounded-2xl">
            <div class="card-body">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Daftar Obat</h3>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Nama Obat</th>
                                <th class="px-4 py-3">Kemasan</th>
                                <th class="px-4 py-3">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-700">
                            @forelse($periksa->detailperiksa as $detail)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">{{ $detail->obat->nama_obat ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $detail->obat->kemasan ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-slate-400">
                                        Tidak ada obat diresepkan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <p class="text-sm text-slate-500">Total Biaya</p>
                    <p class="text-xl font-bold text-slate-800">
                        Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>