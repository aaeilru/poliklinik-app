<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\JadwalPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $stats = [
            'jadwalSaya' => JadwalPeriksa::where('id_dokter', $dokterId)->count(),

            'pasienMenunggu' => DaftarPoli::whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                    $query->where('id_dokter', $dokterId);
                })
                ->whereDoesntHave('periksas')
                ->count(),

            'periksaHariIni' => Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) use ($dokterId) {
                    $query->where('id_dokter', $dokterId);
                })
                ->whereDate('tgl_periksa', today())
                ->count(),

            'totalRiwayat' => Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) use ($dokterId) {
                    $query->where('id_dokter', $dokterId);
                })
                ->count(),

            'pasienSelesai' => DaftarPoli::whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                    $query->where('id_dokter', $dokterId);
                })
                ->whereHas('periksas')
                ->count(),

            'resepDiberikan' => DetailPeriksa::whereHas('periksa.daftarPoli.jadwalPeriksa', function ($query) use ($dokterId) {
                    $query->where('id_dokter', $dokterId);
                })
                ->count(),

            'obatHabis' => Obat::where('stok', '<=', 0)->count(),
        ];

        $pasienMenunggu = DaftarPoli::with([
                'pasien',
                'jadwalPeriksa',
            ])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->whereDoesntHave('periksas')
            ->orderBy('no_antrian', 'asc')
            ->limit(6)
            ->get();

        $riwayatTerbaru = Periksa::with([
                'daftarPoli.pasien',
                'detailPeriksas.obat',
            ])
            ->whereHas('daftarPoli.jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->latest('tgl_periksa')
            ->limit(5)
            ->get();

        $jadwals = JadwalPeriksa::withCount('daftarPolis')
            ->where('id_dokter', $dokterId)
            ->get();

        $obatHabis = Obat::where('stok', '<=', 0)
            ->orderBy('nama_obat')
            ->limit(5)
            ->get();

        return view('dokter.dashboard', compact(
            'stats',
            'pasienMenunggu',
            'riwayatTerbaru',
            'jadwals',
            'obatHabis'
        ));
    }
}