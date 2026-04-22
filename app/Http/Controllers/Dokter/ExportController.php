<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    // ─────────────────────────────────────────────
    // DOKTER: Export Jadwal Periksa milik dokter ini
    // ─────────────────────────────────────────────
    public function jadwal()
    {
        $dokter = Auth::user();

        $jadwals = JadwalPeriksa::where('id_dokter', $dokter->id)
            ->orderBy('hari')
            ->get();

        $filename = 'jadwal-periksa-' . now()->format('Ymd') . '.xls';

        return response()->streamDownload(function () use ($jadwals) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8

            fputcsv($output, ['No', 'Hari', 'Jam Mulai', 'Jam Selesai'], ';');

            foreach ($jadwals as $i => $jadwal) {
                fputcsv($output, [
                    $i + 1,
                    $jadwal->hari,
                    \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i'),
                    \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i'),
                ], ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─────────────────────────────────────────────
    // DOKTER: Export Riwayat Pasien yang sudah diperiksa
    // ─────────────────────────────────────────────
    public function riwayatPasien()
    {
        $dokter = Auth::user();

        // Ambil semua pasien yang sudah diperiksa dokter ini
        $riwayat = DaftarPoli::whereHas('jadwalPeriksa', function ($q) use ($dokter) {
                $q->where('id_dokter', $dokter->id);
            })
            ->whereHas('periksas')
            ->with([
                'pasien',
                'jadwalPeriksa',
                'periksa.detailPeriksas.obat',
            ])
            ->orderByDesc('updated_at')
            ->get();

        $filename = 'riwayat-pasien-' . now()->format('Ymd') . '.xls';

        return response()->streamDownload(function () use ($riwayat) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'No', 'Nama Pasien', 'No. Antrian', 'Jadwal',
                'Tgl Periksa', 'Catatan', 'Obat Diresepkan', 'Total Biaya (Rp)',
            ], ';');

            foreach ($riwayat as $i => $daftar) {
                $periksa = $daftar->periksa;

                // Gabungkan nama-nama obat jadi 1 string
                $namaObat = $periksa
                    ? $periksa->detailPeriksas->map(fn($d) => $d->obat->nama_obat ?? '-')->implode(', ')
                    : '-';

                fputcsv($output, [
                    $i + 1,
                    $daftar->pasien->nama,
                    $daftar->no_antrian,
                    $daftar->jadwalPeriksa->hari . ' ' .
                        \Carbon\Carbon::parse($daftar->jadwalPeriksa->jam_mulai)->format('H:i'),
                    $periksa ? \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') : '-',
                    $periksa->catatan ?? '-',
                    $namaObat,
                    $periksa ? $periksa->total_biaya : 0,
                ], ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
