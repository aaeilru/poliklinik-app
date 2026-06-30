<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\DaftarPoli;
use App\Models\Obat;
use App\Models\Periksa;
use App\Models\Poli;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalPoli' => Poli::count(),
            'totalDokter' => User::where('role', 'dokter')->count(),
            'totalPasien' => User::where('role', 'pasien')->count(),
            'totalObat' => Obat::count(),

            'obatHabis' => Obat::where('stok', '<=', 0)->count(),
            'obatRendah' => Obat::where('stok', '>', 0)
                ->where('stok', '<=', Obat::STOK_MINIMUM)
                ->count(),

            'pendaftaranHariIni' => DaftarPoli::whereDate('created_at', today())->count(),
            'periksaHariIni' => Periksa::whereDate('tgl_periksa', today())->count(),

            'pembayaranPending' => BuktiPembayaran::where('status', 'pending')
                ->whereNotNull('file_bukti')
                ->count(),

            'pembayaranVerified' => BuktiPembayaran::where('status', 'verified')->count(),
        ];

        $obatKritis = Obat::where('stok', '<=', Obat::STOK_MINIMUM)
            ->orderBy('stok', 'asc')
            ->limit(6)
            ->get();

        $pendaftaranTerbaru = DaftarPoli::with([
                'pasien',
                'jadwalPeriksa.dokter.poli',
                'periksas',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $pembayaranTerbaru = BuktiPembayaran::with([
                'periksa.daftarPoli.pasien',
            ])
            ->whereNotNull('file_bukti')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'obatKritis',
            'pendaftaranTerbaru',
            'pembayaranTerbaru'
        ));
    }
}