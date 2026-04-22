<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * Tampilkan semua riwayat pendaftaran poli pasien yang sedang login.
     * Diurutkan dari yang terbaru (order by created_at DESC).
     *
     * EFISIENSI QUERY:
     * Pakai eager loading (with()) untuk menghindari N+1 query problem.
     * Tanpa with(), setiap baris loop akan membuat query terpisah ke DB.
     */
    public function index()
    {
        $pasien = Auth::user();

        // Load semua relasi sekaligus dalam 1 query set (bukan N+1)
        $riwayat = DaftarPoli::where('id_pasien', $pasien->id)
            ->with([
                'jadwalPeriksa.dokter.poli',           // jadwal → dokter → poli
                'periksa.detailPeriksas.obat',         // periksa → obat-obatan
                'periksa.buktiPembayaran',             // status pembayaran
            ])
            ->orderByDesc('created_at')                // terbaru di atas
            ->get();

        return view('pasien.riwayat.index', compact('riwayat'));
    }

    /**
     * Halaman detail 1 pemeriksaan: catatan dokter, obat, total biaya.
     * Hanya bisa diakses jika pasien sudah diperiksa (ada record Periksa).
     */
    public function show($id)
    {
        $pasien = Auth::user();

        // findOrFail: otomatis 404 jika tidak ada
        $daftar = DaftarPoli::where('id_pasien', $pasien->id) // keamanan: hanya milik pasien ini
            ->with([
                'jadwalPeriksa.dokter.poli',
                'periksa.detailPeriksas.obat',
                'periksa.buktiPembayaran',
            ])
            ->findOrFail($id);

        // Pastikan sudah ada hasil periksa sebelum bisa lihat detail
        if (!$daftar->sudahDiperiksa()) {
            return redirect()->route('pasien.riwayat.index')
                ->with('message', 'Belum ada hasil pemeriksaan untuk pendaftaran ini.')
                ->with('type', 'error');
        }

        $periksa = $daftar->periksa;

        return view('pasien.riwayat.show', compact('daftar', 'periksa'));
    }
}
