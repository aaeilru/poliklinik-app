<x-layouts.app title="Dashboard Pasien">

    {{-- ── Flash message ── --}}
    @if(session('message'))
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold
        {{ session('type') === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
        {{ session('message') }}
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         FITUR 1: BANNER ANTRIAN AKTIF
         Hanya tampil jika pasien punya antrian yang belum diperiksa
    ════════════════════════════════════════════════════════════ --}}
    @if($antrian)
    <div class="mb-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 text-white shadow-lg">
        <p class="text-xs font-bold uppercase tracking-widest text-blue-200 mb-3">
            Antrian Aktif Anda
        </p>
        <div class="flex items-center justify-between gap-4">

            {{-- Info Poli & Dokter --}}
            <div class="space-y-1">
                <div>
                    <p class="text-xs text-blue-200">Poliklinik</p>
                    <p class="font-bold text-lg leading-tight">
                        {{ $antrian->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-blue-200">Dokter</p>
                    <p class="font-semibold">{{ $antrian->jadwalPeriksa->dokter->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-blue-200">Jadwal Periksa</p>
                    <p class="font-semibold">
                        {{ $antrian->jadwalPeriksa->hari }}
                        ({{ \Carbon\Carbon::parse($antrian->jadwalPeriksa->jam_mulai)->format('H:i') }} –
                         {{ \Carbon\Carbon::parse($antrian->jadwalPeriksa->jam_selesai)->format('H:i') }})
                    </p>
                </div>
            </div>

            {{-- Nomor Antrian Pasien vs Nomor Dilayani --}}
            <div class="flex gap-4 items-stretch">

                {{-- Nomor antrian pasien (tetap) --}}
                <div class="bg-white/20 rounded-xl px-5 py-3 text-center min-w-[90px]">
                    <p class="text-xs text-blue-100 mb-1">Nomor Anda</p>
                    <p class="text-4xl font-black">{{ $antrian->no_antrian }}</p>
                </div>

                {{-- Nomor sedang dilayani (update real-time via JS) --}}
                <div class="bg-white rounded-xl px-5 py-3 text-center min-w-[90px] shadow">
                    <p class="text-xs text-slate-500 mb-1 font-semibold">Sedang Dilayani</p>
                    {{-- id ini ditarget JS untuk update tanpa refresh --}}
                    <p class="text-4xl font-black text-indigo-600"
                       id="no-dilayani-{{ $antrian->jadwalPeriksa->id }}">
                        {{ $nomorDilayani[$antrian->jadwalPeriksa->id] ?? 0 }}
                    </p>
                    {{-- Indikator live update --}}
                    <p class="text-[10px] text-green-500 font-semibold mt-1">
                        <span class="inline-block w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse mr-1"></span>
                        Live Update
                    </p>
                </div>

            </div>
        </div>
    </div>
    @else
    {{-- Pasien tidak punya antrian aktif --}}
    <div class="mb-6 bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center text-slate-400">
        <i class="fas fa-calendar-xmark text-3xl mb-2 block"></i>
        <p class="font-semibold">Anda belum mendaftar ke poli manapun.</p>
        <a href="{{ route('pasien.daftar') }}"
           class="inline-block mt-3 px-5 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary/90 transition">
            Daftar Sekarang
        </a>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════
         TABEL JADWAL POLIKLINIK + NOMOR SEDANG DILAYANI
    ════════════════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-slate-800">Jadwal Poliklinik</h2>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-4">No</th>
                            <th class="px-5 py-4">Poli</th>
                            <th class="px-5 py-4">Dokter</th>
                            <th class="px-5 py-4">Hari</th>
                            <th class="px-5 py-4">Jam Periksa</th>
                            {{-- Kolom ini di-update real-time oleh JS --}}
                            <th class="px-5 py-4 text-center">Sedang Dilayani</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($jadwals as $i => $jadwal)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                            <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-semibold">
                                {{ $jadwal->dokter->poli->nama_poli ?? '-' }}
                            </td>
                            <td class="px-5 py-3">{{ $jadwal->dokter->nama ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $jadwal->hari }}</td>
                            <td class="px-5 py-3">
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} –
                                {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                {{-- ID unik per jadwal → ditarget JS untuk update --}}
                                <span id="no-dilayani-{{ $jadwal->id }}"
                                      class="inline-block px-4 py-1 text-lg font-black text-indigo-600
                                             bg-indigo-50 rounded-xl border border-indigo-100">
                                    {{ $nomorDilayani[$jadwal->id] ?? 0 }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fas fa-calendar-xmark text-3xl mb-2 block"></i>
                                Belum ada jadwal poliklinik
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════
         FITUR 4: REAL-TIME UPDATE (Dual strategy)
         Strategy 1 — Laravel Echo + Reverb WebSocket (utama)
         Strategy 2 — Polling fallback (jika Reverb belum running)
    ════════════════════════════════════════════════════════════ --}}
    @push('scripts')
    <script>
    /**
     * updateAntrian(idJadwal, noDilayani)
     * ─────────────────────────────────────
     * Fungsi utama: update semua elemen di halaman yang menampilkan
     * nomor antrian untuk jadwal tertentu.
     * Dipanggil baik dari WebSocket maupun dari polling.
     */
    function updateAntrian(idJadwal, noDilayani) {
        // Cari semua elemen dengan id "no-dilayani-{id_jadwal}" dan update teksnya
        document.querySelectorAll('#no-dilayani-' + idJadwal).forEach(el => {
            el.textContent = noDilayani;
            // Animasi kilat supaya user sadar ada update
            el.classList.add('scale-110', 'transition-transform');
            setTimeout(() => el.classList.remove('scale-110'), 300);
        });
    }

    // ── STRATEGY 1: Laravel Echo + Reverb (WebSocket) ────────────────────
    // Jalankan Reverb: php artisan reverb:start
    // Install: npm install --save-dev laravel-echo pusher-js
    if (typeof Echo !== 'undefined') {
        // Listen ke public channel "antrian", event "queue.updated"
        // Sesuai dengan broadcastOn() dan broadcastAs() di QueueUpdated.php
        Echo.channel('antrian')
            .listen('.queue.updated', (event) => {
                // event.id_jadwal dan event.no_dilayani → lihat broadcastWith()
                updateAntrian(event.id_jadwal, event.no_dilayani);
            });
        console.log('[Reverb] WebSocket aktif, mendengarkan channel antrian...');
    } else {
        // ── STRATEGY 2: Polling fallback (setiap 5 detik) ────────────────
        // Berguna saat Reverb belum dikonfigurasi atau tidak running
        console.log('[Polling] Echo tidak tersedia, fallback ke polling setiap 5 detik.');

        setInterval(() => {
            fetch('{{ route("pasien.antrian.live") }}')
                .then(res => res.json())
                .then(data => {
                    // data = { "1": 3, "2": 1, ... } (id_jadwal: no_dilayani)
                    Object.entries(data).forEach(([idJadwal, noDilayani]) => {
                        updateAntrian(idJadwal, noDilayani);
                    });
                })
                .catch(err => console.error('[Polling] Error:', err));
        }, 5000); // 5000ms = 5 detik
    }
    </script>
    @endpush

</x-layouts.app>
