<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * HELPER PRIVATE: Generate response CSV/Excel
     * ═══════════════════════════════════════════════════════
     *
     * Kita pakai format CSV dengan Content-Type Excel agar bisa
     * dibuka langsung di Excel TANPA memerlukan package tambahan
     * (PhpSpreadsheet/Maatwebsite). Cocok untuk kebutuhan soal ini.
     *
     * Format: CSV → Excel akan membuka otomatis karena extension .xls
     */
    private function makeExcelResponse(string $filename, array $headers, array $rows)
    {
        // Mulai output buffer
        $output = fopen('php://output', 'w');

        // BOM UTF-8: agar huruf Indonesia (é, ñ, dll) terbaca benar di Excel
        fputs($output, "\xEF\xBB\xBF");

        // Tulis baris header
        fputcsv($output, $headers, ';'); // pakai separator ; karena standar Excel ID

        // Tulis setiap baris data
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }

        fclose($output);

        // Tidak bisa return buffer karena sudah ditulis ke php://output
        // Jadi kita pakai StreamedResponse Laravel
        return null;
    }

    // ─────────────────────────────────────────────
    // ADMIN: Export Data Dokter
    // ─────────────────────────────────────────────
    public function dokter()
    {
        $dokters = User::where('role', 'dokter')->with('poli')->get();

        $filename = 'data-dokter-' . now()->format('Ymd') . '.xls';

        return response()->streamDownload(function () use ($dokters) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8

            // Header kolom
            fputcsv($output, ['No', 'Nama Dokter', 'Email', 'No. KTP', 'No. HP', 'Alamat', 'Poli'], ';');

            foreach ($dokters as $i => $dokter) {
                fputcsv($output, [
                    $i + 1,
                    $dokter->nama,
                    $dokter->email,
                    $dokter->no_ktp ?? '-',
                    $dokter->no_hp ?? '-',
                    $dokter->alamat ?? '-',
                    $dokter->poli->nama_poli ?? '-',
                ], ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─────────────────────────────────────────────
    // ADMIN: Export Data Pasien
    // ─────────────────────────────────────────────
    public function pasien()
    {
        $pasiens = User::where('role', 'pasien')->get();

        $filename = 'data-pasien-' . now()->format('Ymd') . '.xls';

        return response()->streamDownload(function () use ($pasiens) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, ['No', 'Nama Pasien', 'Email', 'No. RM', 'No. KTP', 'No. HP', 'Alamat'], ';');

            foreach ($pasiens as $i => $pasien) {
                fputcsv($output, [
                    $i + 1,
                    $pasien->nama,
                    $pasien->email,
                    $pasien->no_rm ?? '-',
                    $pasien->no_ktp ?? '-',
                    $pasien->no_hp ?? '-',
                    $pasien->alamat ?? '-',
                ], ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─────────────────────────────────────────────
    // ADMIN: Export Data Obat (termasuk stok & harga)
    // ─────────────────────────────────────────────
    public function obat()
    {
        $obats = Obat::orderBy('nama_obat')->get();

        $filename = 'data-obat-' . now()->format('Ymd') . '.xls';

        return response()->streamDownload(function () use ($obats) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, ['No', 'Nama Obat', 'Kemasan', 'Harga (Rp)', 'Stok', 'Status Stok'], ';');

            foreach ($obats as $i => $obat) {
                // Tentukan status stok untuk kolom deskriptif
                if ($obat->isHabis()) {
                    $statusStok = 'Habis';
                } elseif ($obat->isRendah()) {
                    $statusStok = 'Hampir Habis';
                } else {
                    $statusStok = 'Aman';
                }

                fputcsv($output, [
                    $i + 1,
                    $obat->nama_obat,
                    $obat->kemasan ?? '-',
                    $obat->harga,
                    $obat->stok,
                    $statusStok,
                ], ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
