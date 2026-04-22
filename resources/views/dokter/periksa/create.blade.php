<x-layouts.app title="Input Hasil Periksa">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Input Hasil Periksa</h2>
    </div>

    {{-- Alert error stok habis --}}
    @if(session('message') && session('type') === 'error')
    <div class="mb-4 px-4 py-3 rounded-xl border bg-red-50 text-red-700 border-red-200 text-sm font-semibold">
        <i class="fas fa-triangle-exclamation mr-2"></i>{{ session('message') }}
    </div>
    @endif

    {{-- Info Pasien --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 text-sm">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 font-black text-lg">
                {{ $daftar->no_antrian }}
            </div>
            <div>
                <p class="font-bold text-blue-800 text-base">{{ $daftar->pasien->nama }}</p>
                <p class="text-blue-600 text-xs">
                    {{ $daftar->jadwalPeriksa->hari }},
                    {{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_selesai)->format('H:i') }}
                </p>
                <p class="text-blue-600 text-xs mt-0.5">Keluhan: {{ $daftar->keluhan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">
            <form action="{{ route('periksa.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_daftar_poli" value="{{ $daftar->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    {{-- Catatan Dokter --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Dokter</label>
                        <textarea name="catatan" rows="3"
                                  placeholder="Diagnosa, anjuran, dll..."
                                  class="w-full px-4 py-2 border-2 rounded-lg focus:border-primary focus:outline-none">{{ old('catatan') }}</textarea>
                    </div>

                    {{-- Biaya Periksa --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Biaya Periksa <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center border-2 rounded-lg px-4 py-2 focus-within:border-primary">
                            <span class="text-slate-500 text-sm font-semibold mr-2">Rp</span>
                            <input type="number" name="biaya_periksa" value="{{ old('biaya_periksa', 50000) }}"
                                   min="0" class="w-full focus:outline-none" required>
                        </div>
                        @error('biaya_periksa')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- ── Resep Obat ── --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Resep Obat
                        <span class="font-normal text-slate-400">(opsional, pilih satu atau lebih)</span>
                    </label>

                    @if($obats->isEmpty())
                    {{-- Peringatan semua obat habis --}}
                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                        <i class="fas fa-triangle-exclamation mr-2"></i>
                        Semua obat sedang habis stok. Tidak dapat memberikan resep saat ini.
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($obats as $obat)
                        @php $checked = in_array($obat->id, old('obat_ids', [])); @endphp
                        <label class="flex items-start gap-3 p-3 border-2 rounded-xl cursor-pointer transition
                                      {{ $checked ? 'border-primary bg-primary/5' : 'border-slate-200 hover:border-primary/50' }}">
                            <input type="checkbox" name="obat_ids[]" value="{{ $obat->id }}"
                                   {{ $checked ? 'checked' : '' }}
                                   class="mt-0.5 accent-primary">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-slate-800 truncate">{{ $obat->nama_obat }}</p>
                                <p class="text-xs text-slate-400">{{ $obat->kemasan ?? '-' }}</p>
                                <p class="text-xs font-semibold text-slate-600">
                                    Rp {{ number_format($obat->harga, 0, ',', '.') }}
                                </p>
                                {{-- Badge stok hampir habis --}}
                                @if($obat->isRendah())
                                <span class="text-[10px] text-amber-600 font-bold">
                                    <i class="fas fa-triangle-exclamation"></i> Sisa {{ $obat->stok }}
                                </span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90
                                   text-white font-semibold text-sm transition">
                        <i class="fas fa-save mr-1"></i> Simpan Hasil Periksa
                    </button>
                    <a href="{{ route('periksa.index') }}"
                       class="px-6 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200
                              text-slate-600 font-semibold text-sm transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-layouts.app>
