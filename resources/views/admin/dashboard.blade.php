<x-layouts.app title="Dashboard Admin">

    <style>
        .dashboard-header {
            margin-bottom: 24px;
        }

        .dashboard-title-small {
            color: #4f46e5;
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

        .stat-card.danger {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .stat-card.warning {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label.danger {
            color: #dc2626;
        }

        .stat-label.warning {
            color: #d97706;
        }

        .stat-number {
            color: #1e293b;
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
        }

        .stat-number.danger {
            color: #dc2626;
        }

        .stat-number.warning {
            color: #b45309;
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

        .icon-indigo {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .icon-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .icon-sky {
            background: #e0f2fe;
            color: #0284c7;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .icon-orange {
            background: #ffedd5;
            color: #ea580c;
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

        .shortcut-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .shortcut-card {
            display: block;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            transition: 0.2s ease;
        }

        .shortcut-card:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }

        .shortcut-icon {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .shortcut-title {
            font-weight: 800;
            color: #334155;
        }

        .shortcut-desc {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
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

        .list-item.warning {
            background: #fffbeb;
            border-color: #fde68a;
        }

        @media (max-width: 1200px) {
            .stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .shortcut-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {

            .stat-grid,
            .shortcut-grid {
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
                <p class="dashboard-title-small">Dashboard Admin</p>
                <h1 class="dashboard-title">
                    Selamat Datang, {{ auth()->user()->nama ?? 'Admin' }}
                </h1>
                <p class="dashboard-subtitle">
                    Pantau data poliklinik, stok obat, pendaftaran pasien, dan pembayaran dalam satu halaman.
                </p>
            </div>

            <div class="dashboard-actions">
                <a href="{{ route('obat.index') }}"
                    class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-xl">
                    <i class="fas fa-pills"></i>
                    Kelola Obat
                </a>

                <a href="{{ route('admin.pembayaran.index') }}"
                    class="btn bg-slate-100 hover:bg-slate-200 text-slate-700 border-none rounded-xl">
                    <i class="fas fa-money-check-dollar"></i>
                    Verifikasi Bayar
                </a>
            </div>
        </div>
    </div>

    {{-- Statistik 4 Kolom --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div>
                <p class="stat-label">Total Poli</p>
                <h3 class="stat-number">{{ $stats['totalPoli'] }}</h3>
            </div>
            <div class="stat-icon icon-indigo">
                <i class="fas fa-hospital"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <p class="stat-label">Total Dokter</p>
                <h3 class="stat-number">{{ $stats['totalDokter'] }}</h3>
            </div>
            <div class="stat-icon icon-purple">
                <i class="fas fa-user-doctor"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <p class="stat-label">Total Pasien</p>
                <h3 class="stat-number">{{ $stats['totalPasien'] }}</h3>
            </div>
            <div class="stat-icon icon-sky">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <p class="stat-label">Total Obat</p>
                <h3 class="stat-number">{{ $stats['totalObat'] }}</h3>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-pills"></i>
            </div>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card danger">
            <div>
                <p class="stat-label danger">Obat Habis</p>
                <h3 class="stat-number danger">{{ $stats['obatHabis'] }}</h3>
            </div>
            <div class="stat-icon icon-red">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
        </div>

        <div class="stat-card warning">
            <div>
                <p class="stat-label warning">Stok Rendah</p>
                <h3 class="stat-number warning">{{ $stats['obatRendah'] }}</h3>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-box-open"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <p class="stat-label">Pendaftaran Hari Ini</p>
                <h3 class="stat-number">{{ $stats['pendaftaranHariIni'] }}</h3>
            </div>
            <div class="stat-icon icon-indigo">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <p class="stat-label">Pembayaran Pending</p>
                <h3 class="stat-number">{{ $stats['pembayaranPending'] }}</h3>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="content-grid">
        <div class="space-y-6">
            <div class="panel">
                <h2 class="panel-title">Akses Cepat</h2>
                <p class="panel-subtitle">Menu utama yang sering digunakan admin.</p>

                <div class="shortcut-grid">
                    <a href="{{ route('polis.index') }}" class="shortcut-card">
                        <i class="fas fa-hospital shortcut-icon text-indigo-600"></i>
                        <p class="shortcut-title">Poli</p>
                        <p class="shortcut-desc">Kelola data poli</p>
                    </a>

                    <a href="{{ route('dokter.index') }}" class="shortcut-card">
                        <i class="fas fa-user-doctor shortcut-icon text-purple-600"></i>
                        <p class="shortcut-title">Dokter</p>
                        <p class="shortcut-desc">Kelola data dokter</p>
                    </a>

                    <a href="{{ route('pasien.index') }}" class="shortcut-card">
                        <i class="fas fa-bed-pulse shortcut-icon text-sky-600"></i>
                        <p class="shortcut-title">Pasien</p>
                        <p class="shortcut-desc">Kelola data pasien</p>
                    </a>

                    <a href="{{ route('obat.index') }}" class="shortcut-card">
                        <i class="fas fa-pills shortcut-icon text-emerald-600"></i>
                        <p class="shortcut-title">Obat</p>
                        <p class="shortcut-desc">Kelola stok obat</p>
                    </a>
                </div>
            </div>

            <div class="panel">
                <h2 class="panel-title">Pendaftaran Pasien Terbaru</h2>
                <p class="panel-subtitle">Data pasien yang baru mendaftar ke poli.</p>

                <div class="overflow-x-auto mt-4">
                    <table class="table">
                        <thead>
                            <tr class="text-slate-500">
                                <th>Pasien</th>
                                <th>Dokter</th>
                                <th>Poli</th>
                                <th>Antrian</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftaranTerbaru as $daftar)
                                <tr>
                                    <td class="font-semibold text-slate-700">
                                        {{ $daftar->pasien->nama ?? '-' }}
                                    </td>
                                    <td>{{ $daftar->jadwalPeriksa->dokter->nama ?? '-' }}</td>
                                    <td>{{ $daftar->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-indigo-100 text-indigo-700 border-none font-bold">
                                            #{{ $daftar->no_antrian }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($daftar->periksas->count() > 0)
                                            <span class="badge bg-emerald-100 text-emerald-700 border-none">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="badge bg-amber-100 text-amber-700 border-none">
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-slate-400 py-6">
                                        Belum ada pendaftaran pasien.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="panel-title">Stok Obat Kritis</h2>
                        <p class="panel-subtitle">Obat habis atau hampir habis.</p>
                    </div>

                    <a href="{{ route('obat.index') }}" class="text-sm font-bold text-indigo-600 hover:underline">
                        Lihat
                    </a>
                </div>

                <div class="mt-4">
                    @forelse ($obatKritis as $obat)
                        <div class="list-item {{ $obat->stok <= 0 ? 'danger' : 'warning' }}">
                            <div>
                                <p class="font-bold text-slate-700">{{ $obat->nama_obat }}</p>
                                <p class="text-xs text-slate-500">{{ $obat->kemasan ?? '-' }}</p>
                            </div>

                            @if ($obat->stok <= 0)
                                <span class="badge bg-red-100 text-red-700 border-none font-bold">
                                    Habis
                                </span>
                            @else
                                <span class="badge bg-amber-100 text-amber-700 border-none font-bold">
                                    Stok {{ $obat->stok }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400">
                            <i class="fas fa-circle-check text-3xl text-emerald-500 mb-2"></i>
                            <p class="font-semibold">Semua stok aman.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <h2 class="panel-title">Pembayaran Terbaru</h2>
                <p class="panel-subtitle">Status bukti pembayaran pasien.</p>

                <div class="mt-4">
                    @forelse ($pembayaranTerbaru as $bukti)
                        <div class="list-item">
                            <div>
                                <p class="font-bold text-slate-700">
                                    {{ $bukti->periksa->daftarPoli->pasien->nama ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $bukti->created_at ? $bukti->created_at->format('d M Y H:i') : '-' }}
                                </p>
                            </div>

                            @if ($bukti->status === 'verified')
                                <span class="badge bg-emerald-100 text-emerald-700 border-none">
                                    Verified
                                </span>
                            @else
                                <span class="badge bg-amber-100 text-amber-700 border-none">
                                    Pending
                                </span>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-slate-400 py-6">
                            Belum ada pembayaran.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
