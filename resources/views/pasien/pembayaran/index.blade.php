<x-layouts.app title="Pembayaran">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Pembayaran</h2>
    </div>

    @if(session('message'))
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold border
        {{ session('type') === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' }}">
        {{ session('message') }}
    </div>
    @endif

    @forelse($tagihans as $daftar)
    @php
        $periksa = $daftar->periksa;
        $bukti   = $periksa?->buktiPembayaran;
        $total   = $periksa?->total_biaya ?? 0;
    @endphp

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-5 overflow-hidden">

        {{-- Header kartu --}}
        <div class="flex items-center justify-between px-5 py-4 bg-slate-50 border-b border-slate-200">
            <div>
                <p class="font-bold text-slate-800">
                    {{ $daftar->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                    <span class="text-slate-400 font-normal">·</span>
                    {{ $daftar->jadwalPeriksa->dokter->nama ?? '-' }}
                </p>
                <p class="text-xs text-slate-400">
                    Diperiksa: {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d M Y, H:i') }}
                </p>
            </div>
            {{-- Badge status --}}
            @if($bukti && $bukti->isVerified())
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full
                             bg-green-100 text-green-700 font-bold text-xs">
                    <i class="fas fa-circle-check"></i> Lunas
                </span>
            @elseif($bukti && $bukti->file_bukti)
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full
                             bg-amber-100 text-amber-700 font-bold text-xs">
                    <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full
                             bg-red-100 text-red-700 font-bold text-xs">
                    <i class="fas fa-circle-xmark"></i> Belum Bayar
                </span>
            @endif
        </div>

        <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Rincian tagihan --}}
            <div>
                <p class="text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Rincian Tagihan</p>
                <div class="space-y-1 text-sm text-slate-600">
                    <div class="flex justify-between">
                        <span>Biaya Periksa</span>
                        <span>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
                    </div>
                    @foreach($periksa->detailPeriksas as $detail)
                    <div class="flex justify-between text-slate-500">
                        <span>{{ $detail->obat->nama_obat }}</span>
                        <span>Rp {{ number_format($detail->obat->harga, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between font-bold text-indigo-700 border-t border-slate-200 pt-2 mt-1">
                        <span>Total</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Upload / preview bukti --}}
            <div>
                <p class="text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Bukti Pembayaran</p>

                @if($bukti && $bukti->isVerified())
                    {{-- Sudah lunas: tampilkan bukti saja --}}
                    <img src="{{ Storage::url($bukti->file_bukti) }}"
                         alt="Bukti Pembayaran"
                         class="w-full max-h-48 object-contain rounded-xl border border-green-200">
                    <p class="text-xs text-green-600 font-semibold mt-2">
                        <i class="fas fa-circle-check mr-1"></i> Pembayaran telah diverifikasi admin
                    </p>

                @elseif($bukti && $bukti->file_bukti)
                    {{-- Sudah upload, menunggu verifikasi --}}
                    <img src="{{ Storage::url($bukti->file_bukti) }}"
                         alt="Bukti Pembayaran"
                         class="w-full max-h-48 object-contain rounded-xl border border-amber-200 mb-2">
                    <p class="text-xs text-amber-600 font-semibold mb-3">
                        <i class="fas fa-hourglass-half mr-1"></i> Menunggu verifikasi admin
                    </p>
                    {{-- Boleh upload ulang --}}
                    <form action="{{ route('pasien.pembayaran.upload', $periksa->id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="block text-xs text-slate-500 mb-1">Upload ulang jika ada kesalahan:</label>
                        <div class="flex gap-2">
                            <input type="file" name="file_bukti" accept="image/*" required
                                   class="text-xs border rounded-lg px-3 py-1.5 w-full">
                            <button type="submit"
                                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs
                                           font-semibold rounded-lg transition whitespace-nowrap">
                                Upload Ulang
                            </button>
                        </div>
                    </form>

                @else
                    {{-- Belum upload --}}
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-5 text-center text-slate-400 mb-3">
                        <i class="fas fa-image text-2xl mb-2 block"></i>
                        <p class="text-xs">Belum ada bukti pembayaran</p>
                    </div>
                    <form action="{{ route('pasien.pembayaran.upload', $periksa->id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Upload Foto Bukti Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-slate-400 mb-2">Format: JPG/PNG, maks. 2MB</p>
                        <input type="file" name="file_bukti" accept="image/*" required
                               class="border-2 rounded-lg px-3 py-1.5 w-full text-sm mb-2">
                        @error('file_bukti')
                            <p class="text-xs text-red-500 mb-1">{{ $message }}</p>
                        @enderror
                        <button type="submit"
                                class="w-full py-2 bg-primary hover:bg-primary/90 text-white text-sm
                                       font-semibold rounded-lg transition">
                            <i class="fas fa-upload mr-1"></i> Upload Bukti Pembayaran
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>

    @empty
    <div class="text-center py-20 text-slate-400">
        <i class="fas fa-receipt text-4xl mb-3 block"></i>
        <p class="font-semibold">Belum ada tagihan</p>
    </div>
    @endforelse

</x-layouts.app>
