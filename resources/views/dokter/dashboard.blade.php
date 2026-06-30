<x-layouts.app title="Dashboard Dokter">

    <style>
        .dashboard-header {
            margin-bottom: 24px;
        }

        .dashboard-title-small {
            color: #7c3aed;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .dashboard-title {
            color: #1e293b;
            font-size: 30px;
            line-height: 1.2;
            font-weight: 800;
            margin: 0;
        }

        .dashboard-subtitle {
            color: #64748b;
            font-size: 15px;
            margin-top: 8px;
        }

        .dashboard-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .dashboard-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 18px;
            min-height: 112px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-card.warning {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .stat-card.danger {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label.warning {
            color: #d97706;
        }

        .stat-label.danger {
            color: #dc2626;
        }

        .stat-number {
            color: #1e293b;
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
        }

        .stat-number.warning {
            color: #b45309;
        }

        .stat-number.danger {
            color: #dc2626;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .icon-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .icon-orange {
            background: #ffedd5;
            color: #ea580c;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .icon-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(340px, 1fr);
            gap: 24px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        }

        .panel-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }

        .panel-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }

        .list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            margin-top: 10px;
        }

        .list-item.danger {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .list-item.purple {
            background: #faf5ff;
            border-color: #e9d5ff;
        }

        .summary-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            margin-top: 10px;
        }

        .summary-icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        @media (max-width: 1200px) {
            .stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-title {
                font-size: 24px;
            }
        }
    </style>

    {{-- Header --}}
    <div class="dashboard-header">
        <div class="dashboard-header-row">
            <div>
                <p class="dashboard-title-small">Dashboard Dokter</p>
                <h1 class="dashboard-title">
                    Selamat Datang, {{ auth()->user()->nama ?? 'Dokter' }}
                </h1>
                <p class="dashboard-subtitle">
                    Pantau jadwal periksa, pasien menunggu, riwayat pemeriksaan, dan status stok obat.
                </p>
            </div>

            <div class="dashboard-actions">
                <a href="{{ route('periksa-pasien.index') }}"
                    class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-xl">
                    <i class="fas fa-user-injured"></i>
                    Periksa Pasien
                </a>

                <a href="{{ route('jadwal-periksa.index') }}"
                    class="btn bg-slate-100 hover:bg-slate-200 text-slate-700 border-none rounded-xl">
                    <i class="fas fa-calendar-days"></i>
                    Jadwal Saya
                </a>
            </div>
        </div>
    </div>

    {{-- Statistik 4 Kolom --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div>
                <p class="stat-label">Jadwal Saya</p>
                <h3 class="stat-number">{{ $stats['jadwalSaya'] }}</h3>
            </div>
            <div class="stat-icon icon-purple">
                <i class="fas fa-calendar-days"></i>
            </div>
        </div>

        <div class="stat-card warning">
            <div>
                <p class="stat-label warning">Pasien Menunggu</p>
                <h3 class="stat-number warning">{{ $stats['pasienMenunggu'] }}</h3>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <p class="stat-label">Periksa Hari Ini</p>
                <h3 class="stat-number">{{ $stats['periksaHariIni'] }}</h3>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-stethoscope"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <p class="stat-label">Total Riwayat</p>
                <h3 class="stat-number">{{ $stats['totalRiwayat'] }}</h3>
            </div>
            <div class="stat-icon icon-blue">
                <i class="fas fa-file-medical"></i>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="content-grid">

        {{-- Kiri --}}
        <div class="space-y-6">
            <div class="panel">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h2 class="panel-title">Pasien Menunggu Pemeriksaan</h2>
                        <p class="panel-subtitle">Daftar pasien yang belum diperiksa.</p>
                    </div>

                    <a href="{{ route('periksa-pasien.index') }}"
                        class="text-sm font-bold text-indigo-600 hover:underline">
                        Lihat semua
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr class="text-slate-500">
                                <th>Antrian</th>
                                <th>Pasien</th>
                                <th>Keluhan</th>
                                <th>Jadwal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pasienMenunggu as $daftar)
                                <tr>
                                    <td>
                                        <span class="badge bg-indigo-100 text-indigo-700 border-none font-bold">
                                            #{{ $daftar->no_antrian }}
                                        </span>
                                    </td>

                                    <td class="font-semibold text-slate-700">
                                        {{ $daftar->pasien->nama ?? '-' }}
                                    </td>

                                    <td class="max-w-[260px] truncate">
                                        {{ $daftar->keluhan ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $daftar->jadwalPeriksa->hari ?? '-' }},
                                        {{ isset($daftar->jadwalPeriksa->jam_mulai) ? substr($daftar->jadwalPeriksa->jam_mulai, 0, 5) : '-' }}
                                        -
                                        {{ isset($daftar->jadwalPeriksa->jam_selesai) ? substr($daftar->jadwalPeriksa->jam_selesai, 0, 5) : '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('periksa-pasien.create', $daftar->id) }}"
                                            class="btn btn-sm bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg">
                                            Periksa
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-slate-400 py-8">
                                        Tidak ada pasien yang menunggu pemeriksaan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h2 class="panel-title">Riwayat Pemeriksaan Terbaru</h2>
                        <p class="panel-subtitle">Data pemeriksaan terakhir yang dilakukan dokter.</p>
                    </div>

                    <a href="{{ route('dokter.riwayat') }}" class="text-sm font-bold text-indigo-600 hover:underline">
                        Lihat riwayat
                    </a>
                </div>

                @forelse ($riwayatTerbaru as $periksa)
                    <div class="list-item">
                        <div>
                            <p class="font-bold text-slate-800">
                                {{ $periksa->daftarPoli->pasien->nama ?? '-' }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $periksa->tgl_periksa ? \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d M Y H:i') : '-' }}
                            </p>

                            <p class="text-sm text-slate-600 mt-2">
                                Catatan: {{ $periksa->catatan ?: '-' }}
                            </p>

                            <div class="flex flex-wrap gap-2 mt-3">
                                @forelse ($periksa->detailPeriksas as $detail)
                                    <span class="badge bg-indigo-100 text-indigo-700 border-none">
                                        {{ $detail->obat->nama_obat ?? '-' }}
                                    </span>
                                @empty
                                    <span class="badge bg-slate-100 text-slate-500 border-none">
                                        Tanpa obat
                                    </span>
                                @endforelse
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-xs text-slate-400">Biaya</p>
                            <p class="font-extrabold text-slate-800">
                                Rp{{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-400 py-8">
                        Belum ada riwayat pemeriksaan.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Kanan --}}
        <div class="space-y-6">
            <div class="panel">
                <h2 class="panel-title">Jadwal Saya</h2>
                <p class="panel-subtitle">Jadwal praktik dokter.</p>

                <div class="mt-4">
                    @forelse ($jadwals as $jadwal)
                        <div class="list-item purple">
                            <div>
                                <p class="font-extrabold text-purple-700">
                                    {{ $jadwal->hari }}
                                </p>
                                <p class="text-sm text-slate-600">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }}
                                    -
                                    {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </p>
                            </div>

                            <span class="badge bg-white text-purple-700 border border-purple-200">
                                {{ $jadwal->daftar_polis_count }} pasien
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-slate-400 py-6">
                            Belum ada jadwal periksa.
                        </p>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <h2 class="panel-title">Ringkasan Praktik</h2>

                <div class="summary-box">
                    <div class="flex items-center gap-3">
                        <div class="summary-icon icon-green">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700">Pasien Selesai</p>
                            <p class="text-xs text-slate-400">Total sudah diperiksa</p>
                        </div>
                    </div>
                    <p class="text-xl font-extrabold text-slate-800">
                        {{ $stats['pasienSelesai'] }}
                    </p>
                </div>

                <div class="summary-box">
                    <div class="flex items-center gap-3">
                        <div class="summary-icon icon-blue">
                            <i class="fas fa-prescription-bottle-medical"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700">Resep Diberikan</p>
                            <p class="text-xs text-slate-400">Total detail obat</p>
                        </div>
                    </div>
                    <p class="text-xl font-extrabold text-slate-800">
                        {{ $stats['resepDiberikan'] }}
                    </p>
                </div>

                <div class="summary-box danger">
                    <div class="flex items-center gap-3">
                        <div class="summary-icon icon-red">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <p class="font-bold text-red-700">Obat Habis</p>
                            <p class="text-xs text-red-400">Perlu ditambah admin</p>
                        </div>
                    </div>
                    <p class="text-xl font-extrabold text-red-700">
                        {{ $stats['obatHabis'] }}
                    </p>
                </div>
            </div>

            <div class="panel">
                <h2 class="panel-title">Info Obat Habis</h2>
                <p class="panel-subtitle">Obat yang tidak bisa digunakan untuk resep.</p>

                <div class="mt-4">
                    @forelse ($obatHabis as $obat)
                        <div class="list-item danger">
                            <div>
                                <p class="font-bold text-slate-700">{{ $obat->nama_obat }}</p>
                                <p class="text-xs text-slate-500">{{ $obat->kemasan ?? '-' }}</p>
                            </div>

                            <span class="badge bg-red-100 text-red-700 border-none font-bold">
                                Habis
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400">
                            <i class="fas fa-circle-check text-3xl text-emerald-500 mb-2"></i>
                            <p class="font-semibold">Tidak ada obat habis.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
