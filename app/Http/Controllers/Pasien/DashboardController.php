<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard pasien.
     *
     * Menampilkan:
     * 1. Banner antrian aktif pasien (jika ada)
     * 2. Tabel semua jadwal poliklinik + nomor antrian yang sedang dilayani
     *
     * CONSTRAINT BISNIS:
     * - Pasien tidak bisa daftar 2 poli sekaligus
     * - Pasien hanya bisa daftar lagi setelah pemeriksaan selesai
     * Kedua constraint ini dicek di PoliController saat submit pendaftaran.
     */
    public function index()
    {
        $pasien = Auth::user();

        // ── Cari antrian aktif pasien ────────────────────────────────────────
        // "Aktif" = sudah daftar tapi BELUM diperiksa (belum ada record Periksa)
        $antrian = DaftarPoli::where('id_pasien', $pasien->id)
            ->whereDoesntHave('periksas')
            ->with(['jadwalPeriksa.dokter.poli'])
            ->first(); // hanya 1 antrian aktif yang boleh ada

        // ── Nomor antrian yang SEDANG DILAYANI per jadwal ────────────────────
        // "Sedang dilayani" = antrian dengan no terkecil yang SUDAH diperiksa
        // (ada record di tabel periksa)
        // Kita ambil per id_jadwal: MAX no_antrian dari pasien sudah diperiksa
        // = nomor terakhir yang sudah dipanggil dokter
        $nomorDilayani = DaftarPoli::whereHas('periksas')
            ->selectRaw('id_jadwal, MAX(no_antrian) as no_dilayani')
            ->groupBy('id_jadwal')
            ->pluck('no_dilayani', 'id_jadwal'); // key: id_jadwal, value: no_dilayani

        // ── Semua jadwal periksa untuk tabel dashboard ───────────────────────
        $jadwals = JadwalPeriksa::with(['dokter.poli'])
            ->orderBy('hari')
            ->get();

        return view('pasien.dashboard', compact('antrian', 'jadwals', 'nomorDilayani'));
    }

    /**
     * API endpoint: ambil nomor antrian yang sedang dilayani per jadwal.
     * Dipanggil oleh JavaScript setiap beberapa detik (polling).
     * Jika pakai Reverb/WebSocket, endpoint ini juga tetap dipakai sebagai fallback.
     *
     * Return: JSON { id_jadwal: no_dilayani, ... }
     */
    public function getAntrianLive()
    {
        $data = DaftarPoli::whereHas('periksas')
            ->selectRaw('id_jadwal, MAX(no_antrian) as no_dilayani')
            ->groupBy('id_jadwal')
            ->pluck('no_dilayani', 'id_jadwal');

        return response()->json($data);
    }
}
