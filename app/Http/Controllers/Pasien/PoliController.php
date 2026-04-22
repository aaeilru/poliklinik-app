<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliController extends Controller
{
    public function get()
    {
        $user    = Auth::user();
        $polis   = Poli::all();
        $jadwal  = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', [
            'user'    => $user,
            'polis'   => $polis,
            'jadwals' => $jadwal,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli'    => 'required|exists:poli,id',
            'id_jadwal'  => 'required|exists:jadwal_periksa,id',
            'keluhan'    => 'nullable|string',
            'id_pasien'  => 'required|exists:users,id',
        ]);

        $pasienId = $request->id_pasien;

        // ── CONSTRAINT 1: Tidak boleh daftar jika masih ada antrian aktif ──
        // "Aktif" = DaftarPoli yang belum punya record Periksa (belum diperiksa)
        $antrianAktif = DaftarPoli::where('id_pasien', $pasienId)
            ->whereDoesntHave('periksas')
            ->exists();

        if ($antrianAktif) {
            return redirect()->back()
                ->with('message', 'Anda masih memiliki antrian aktif. Selesaikan pemeriksaan terlebih dahulu sebelum mendaftar kembali.')
                ->with('type', 'error');
        }

        // ── CONSTRAINT 2: Tidak boleh daftar ke jadwal yang sama 2x ──
        // (edge case: pasien sudah diperiksa di jadwal ini tapi coba daftar lagi)
        $sudahDaftarJadwal = DaftarPoli::where('id_pasien', $pasienId)
            ->where('id_jadwal', $request->id_jadwal)
            ->whereDoesntHave('periksas') // hanya cek yang belum selesai
            ->exists();

        if ($sudahDaftarJadwal) {
            return redirect()->back()
                ->with('message', 'Anda sudah terdaftar di jadwal ini.')
                ->with('type', 'error');
        }

        // ── Hitung nomor antrian ──────────────────────────────────────────
        // Nomor antrian = jumlah yang sudah daftar di jadwal ini + 1
        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();

        DaftarPoli::create([
            'id_pasien'   => $pasienId,
            'id_jadwal'   => $request->id_jadwal,
            'keluhan'     => $request->keluhan,
            'no_antrian'  => $jumlahSudahDaftar + 1,
        ]);

        return redirect()->route('pasien.dashboard')
            ->with('message', 'Berhasil mendaftar ke Poli! Silakan cek nomor antrian Anda di dashboard.')
            ->with('type', 'success');
    }
}
