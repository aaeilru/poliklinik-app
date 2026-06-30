<x-layouts.app title="Data Obat">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Data Obat</h2>
        <div class="flex gap-2">
            {{-- ── Fitur 5: Export Excel ── --}}
            <a href="{{ route('admin.export.obat') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700
                      text-white text-sm font-semibold rounded-xl transition">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('obat.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90
                      text-white text-sm font-semibold rounded-xl transition">
                <i class="fas fa-plus text-xs"></i> Tambah Obat
            </a>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('message'))
        <div
            class="mb-4 px-4 py-3 rounded-xl text-sm font-semibold border
        {{ session('type') === 'success' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200' }}">
            {{ session('message') }}
        </div>
    @endif

    {{-- Legenda stok --}}
    <div class="flex gap-3 mb-4 text-xs flex-wrap">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-600 font-semibold">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span> Stok Habis (= 0)
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-600 font-semibold">
            <span class="w-2 h-2 bg-amber-500 rounded-full"></span> Stok Rendah (≤ 10)
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 text-green-600 font-semibold">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span> Stok Aman (&gt; 10)
        </span>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Obat</th>
                            <th class="px-6 py-4">Kemasan</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4">Stok</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($obats as $obat)
                            {{-- Warna baris sesuai status stok --}}
                            <tr
                                class="border-t border-slate-100 transition
                            {{ $obat->isHabis() ? 'bg-red-50' : ($obat->isRendah() ? 'bg-amber-50' : 'hover:bg-slate-50') }}">

                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $obat->nama_obat }}</td>

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">
                                        {{ $obat->kemasan ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    Rp {{ number_format($obat->harga, 0, ',', '.') }}
                                </td>

                                {{-- Kolom stok dengan badge visual ── Fitur 2 ── --}}
                                <td class="px-6 py-4">
                                    @if ($obat->isHabis())
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-600">
                                            <i class="fas fa-xmark"></i> Habis
                                        </span>
                                    @elseif($obat->isRendah())
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-600">
                                            <i class="fas fa-triangle-exclamation"></i> {{ $obat->stok }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-600">
                                            <i class="fas fa-check"></i> {{ $obat->stok }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('obat.edit', $obat->id) }}"
                                            class="inline-flex items-center gap-1 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition">
                                            <i class="fas fa-pen text-xs"></i> Edit
                                        </a>
                                        <form action="{{ route('obat.destroy', $obat->id) }}" method="POST"
                                            class="form-confirm" data-title="Hapus data obat?"
                                            data-text="Data obat yang dihapus tidak dapat dikembalikan."
                                            data-icon="warning" data-confirm="Ya, hapus" data-cancel="Batal">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-error btn-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">
                                    <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                    Belum ada data obat
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($obats->where('stok', '<=', 0)->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Stok Obat Habis',
                    html: `
                    <div style="text-align:left">
                        <p>Beberapa obat memiliki stok habis dan tidak dapat digunakan untuk resep pasien.</p>
                        <ul style="margin-top:10px; padding-left:18px;">
                            @foreach ($obats->where('stok', '<=', 0) as $obat)
                                <li><b>{{ $obat->nama_obat }}</b> - stok {{ $obat->stok }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#2d4499'
                });
            });
        </script>
    @endif

    @php
        $obatHabis = $obats
            ->where('stok', '<=', 0)
            ->map(function ($obat) {
                return [
                    'nama_obat' => $obat->nama_obat,
                    'kemasan' => $obat->kemasan,
                    'stok' => $obat->stok,
                ];
            })
            ->values();
    @endphp

    @if ($obatHabis->count() > 0)
        <style>
            .stok-alert-popup {
                border-radius: 24px !important;
                padding: 28px 28px 24px !important;
            }

            .stok-alert-title {
                font-size: 24px !important;
                font-weight: 800 !important;
                color: #1f2937 !important;
                margin-bottom: 8px !important;
            }

            .stok-alert-html {
                margin: 0 !important;
            }

            .stok-alert-wrapper {
                text-align: left;
                font-family: inherit;
            }

            .stok-alert-summary {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #fff7ed;
                border: 1px solid #fed7aa;
                color: #9a3412;
                padding: 12px 14px;
                border-radius: 14px;
                margin-bottom: 16px;
                font-size: 14px;
                line-height: 1.5;
            }

            .stok-alert-count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 28px;
                height: 28px;
                border-radius: 999px;
                background: #fb923c;
                color: white;
                font-weight: 800;
                font-size: 13px;
                flex-shrink: 0;
            }

            .stok-alert-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-top: 8px;
            }

            .stok-alert-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 14px;
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 14px;
            }

            .stok-alert-number {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                border-radius: 10px;
                background: #fee2e2;
                color: #dc2626;
                font-weight: 800;
                font-size: 13px;
                flex-shrink: 0;
            }

            .stok-alert-content {
                flex: 1;
                min-width: 0;
            }

            .stok-alert-name {
                font-weight: 800;
                color: #1f2937;
                font-size: 14px;
                margin-bottom: 2px;
            }

            .stok-alert-meta {
                color: #64748b;
                font-size: 12px;
            }

            .stok-alert-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 5px 9px;
                border-radius: 999px;
                background: #fee2e2;
                color: #dc2626;
                font-size: 12px;
                font-weight: 800;
                flex-shrink: 0;
            }

            .stok-alert-note {
                margin-top: 14px;
                color: #64748b;
                font-size: 13px;
                line-height: 1.5;
                text-align: center;
            }

            .stok-alert-confirm {
                border-radius: 12px !important;
                padding: 10px 22px !important;
                font-weight: 700 !important;
                background: #2d4499 !important;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const obatHabis = @json($obatHabis);

                function escapeHtml(value) {
                    return String(value ?? '').replace(/[&<>"']/g, function(char) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;'
                        } [char];
                    });
                }

                const listObat = obatHabis.map((obat, index) => {
                    return `
                    <div class="stok-alert-item">
                        <div class="stok-alert-number">${index + 1}</div>

                        <div class="stok-alert-content">
                            <div class="stok-alert-name">${escapeHtml(obat.nama_obat)}</div>
                            <div class="stok-alert-meta">
                                ${escapeHtml(obat.kemasan)} · Stok tersedia: ${escapeHtml(obat.stok)}
                            </div>
                        </div>

                        <div class="stok-alert-badge">
                            <i class="fas fa-times"></i>
                            Habis
                        </div>
                    </div>
                `;
                }).join('');

                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Obat Habis',
                    width: 560,
                    html: `
                    <div class="stok-alert-wrapper">
                        <div class="stok-alert-summary">
                            <span class="stok-alert-count">${obatHabis.length}</span>
                            <span>
                                Terdapat <b>${obatHabis.length} obat</b> dengan stok habis.
                            </span>
                        </div>

                        <div class="stok-alert-list">
                            ${listObat}
                        </div>

                        <div class="stok-alert-note">
                            Silakan lakukan penambahan stok melalui tombol <b>Edit</b> pada data obat terkait.
                        </div>
                    </div>
                `,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#2d4499',
                    showCloseButton: true,
                    customClass: {
                        popup: 'stok-alert-popup',
                        title: 'stok-alert-title',
                        htmlContainer: 'stok-alert-html',
                        confirmButton: 'stok-alert-confirm'
                    }
                });
            });
        </script>
    @endif

</x-layouts.app>
