<x-layouts.app title="Verifikasi Pembayaran">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Verifikasi Pembayaran</h2>
    </div>

    @if(session('message'))
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold border
        {{ session('type') === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' }}">
        {{ session('message') }}
    </div>
    @endif

    {{-- Filter tab --}}
    <div class="flex gap-3 mb-5">
        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-amber-100 text-amber-700">
            Pending: {{ $tagihans->where('status','pending')->count() }}
        </span>
        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-green-100 text-green-700">
            Lunas: {{ $tagihans->where('status','verified')->count() }}
        </span>
    </div>

    @forelse($tagihans as $bukti)
    @php
        $periksa = $bukti->periksa;
        $daftar  = $periksa->daftarPoli;
        $pasien  = $daftar->pasien;
        $total   = $periksa->total_biaya;
    @endphp

    <div class="bg-white border rounded-2xl shadow-sm mb-5 overflow-hidden
        {{ $bukti->isVerified() ? 'border-green-200' : 'border-amber-200' }}">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4
            {{ $bukti->isVerified() ? 'bg-green-50 border-b border-green-100' : 'bg-amber-50 border-b border-amber-100' }}">
            <div>
                <p class="font-bold text-slate-800">{{ $pasien->nama }}</p>
                <p class="text-xs text-slate-500">
                    {{ $daftar->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }} ·
                    Dr. {{ $daftar->jadwalPeriksa->dokter->nama ?? '-' }}
                </p>
                <p class="text-xs text-slate-400">
                    Periksa: {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d M Y, H:i') }}
                </p>
            </div>
            @if($bukti->isVerified())
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full
                             bg-green-100 text-green-700 font-bold text-xs">
                    <i class="fas fa-circle-check"></i> Lunas
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full
                             bg-amber-100 text-amber-700 font-bold text-xs">
                    <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi
                </span>
            @endif
        </div>

        <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Rincian tagihan --}}
            <div>
                <p class="text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Rincian</p>
                <div class="space-y-1 text-sm text-slate-600">
                    <div class="flex justify-between">
                        <span>Biaya Periksa</span>
                        <span>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
                    </div>
                    @foreach($periksa->detailPeriksas as $detail)
                    <div class="flex justify-between text-slate-400">
                        <span>{{ $detail->obat->nama_obat }}</span>
                        <span>Rp {{ number_format($detail->obat->harga, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between font-bold text-indigo-700 border-t pt-1">
                        <span>Total</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Bukti pembayaran + tombol verifikasi --}}
            <div>
                <p class="text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Bukti Upload Pasien</p>
                <img src="{{ Storage::url($bukti->file_bukti) }}"
                     alt="Bukti Pembayaran"
                     class="w-full max-h-48 object-contain rounded-xl border border-slate-200 mb-3">

                @if(!$bukti->isVerified())
                {{-- Tombol konfirmasi hanya muncul jika belum verified --}}
                <form action="{{ route('admin.pembayaran.verify', $bukti->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')"
                            class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold
                                   text-sm rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-circle-check"></i> Konfirmasi Lunas
                    </button>
                </form>
                @else
                <p class="text-xs text-green-600 font-semibold text-center">
                    <i class="fas fa-circle-check mr-1"></i> Sudah diverifikasi
                </p>
                @endif
            </div>

        </div>
    </div>

    @empty
    <div class="text-center py-20 text-slate-400">
        <i class="fas fa-inbox text-4xl mb-3 block"></i>
        <p class="font-semibold">Tidak ada bukti pembayaran yang menunggu verifikasi</p>
    </div>
    @endforelse

</x-layouts.app>
