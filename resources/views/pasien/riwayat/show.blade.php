<x-layouts.app title="Detail Pemeriksaan">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.riwayat.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Detail Pemeriksaan</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Kolom Kiri: Info Poli & Jadwal ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Card Info Pendaftaran --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">Info Pendaftaran</p>

                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-slate-400 text-xs">Poliklinik</p>
                        <p class="font-bold text-slate-800">
                            {{ $daftar->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Dokter</p>
                        <p class="font-semibold text-slate-700">{{ $daftar->jadwalPeriksa->dokter->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Jadwal</p>
                        <p class="font-semibold text-slate-700">
                            {{ $daftar->jadwalPeriksa->hari }},
                            {{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_selesai)->format('H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">No. Antrian</p>
                        <p class="font-bold text-indigo-600 text-xl">{{ $daftar->no_antrian }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Tanggal Periksa</p>
                        <p class="font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card Status Pembayaran --}}
            @php $bukti = $periksa->buktiPembayaran; @endphp
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">Status Pembayaran</p>
                @if($bukti && $bukti->isVerified())
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                 bg-green-100 text-green-700 font-bold text-sm">
                        <i class="fas fa-circle-check"></i> Lunas
                    </span>
                @elseif($bukti && $bukti->file_bukti)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                 bg-amber-100 text-amber-700 font-bold text-sm">
                        <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                 bg-red-100 text-red-700 font-bold text-sm">
                        <i class="fas fa-circle-xmark"></i> Belum Bayar
                    </span>
                @endif
                <div class="mt-3">
                    <a href="{{ route('pasien.pembayaran.index') }}"
                       class="text-xs text-primary font-semibold hover:underline">
                        <i class="fas fa-arrow-right mr-1"></i>Ke halaman pembayaran
                    </a>
                </div>
            </div>

        </div>

        {{-- ── Kolom Kanan: Hasil Periksa ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Catatan Dokter --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">
                    <i class="fas fa-notes-medical mr-1"></i> Catatan Dokter
                </p>
                <p class="text-sm text-slate-700 leading-relaxed">
                    {{ $periksa->catatan ?? 'Tidak ada catatan.' }}
                </p>
            </div>

            {{-- Daftar Obat --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">
                    <i class="fas fa-pills mr-1"></i> Obat yang Diresepkan
                </p>

                @if($periksa->detailPeriksas->isEmpty())
                    <p class="text-sm text-slate-400">Tidak ada obat yang diresepkan.</p>
                @else
                    <div class="space-y-2">
                        @foreach($periksa->detailPeriksas as $detail)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="font-semibold text-sm text-slate-800">{{ $detail->obat->nama_obat }}</p>
                                <p class="text-xs text-slate-400">{{ $detail->obat->kemasan ?? '-' }}</p>
                            </div>
                            <p class="font-semibold text-sm text-slate-700">
                                Rp {{ number_format($detail->obat->harga, 0, ',', '.') }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Total Biaya --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div class="space-y-1 text-sm text-slate-600">
                        <p>Biaya Periksa: <span class="font-semibold">Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span></p>
                        <p>Biaya Obat:
                            <span class="font-semibold">
                                Rp {{ number_format($periksa->detailPeriksas->sum(fn($d) => $d->obat->harga ?? 0), 0, ',', '.') }}
                            </span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wider mb-1">Total Biaya</p>
                        <p class="text-2xl font-black text-indigo-700">
                            Rp {{ number_format($periksa->total_biaya, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-layouts.app>
