<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;

class PembayaranController extends Controller
{
    /**
     * Daftar semua tagihan yang menunggu verifikasi (status pending dengan file).
     * Admin perlu melihat bukti upload lalu konfirmasi.
     */
    public function index()
    {
        // Ambil semua bukti yang sudah diupload (file_bukti tidak null)
        // eager load relasi supaya tidak N+1
        $tagihans = BuktiPembayaran::with([
                'periksa.daftarPoli.pasien',
                'periksa.daftarPoli.jadwalPeriksa.dokter.poli',
                'periksa.detailPeriksas.obat',
            ])
            ->whereNotNull('file_bukti')     // hanya yang sudah upload bukti
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END") // pending dulu
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.pembayaran.index', compact('tagihans'));
    }

    /**
     * Konfirmasi/verifikasi pembayaran → ubah status jadi 'verified' (lunas).
     */
    public function verify($id)
    {
        $bukti = BuktiPembayaran::findOrFail($id);

        $bukti->update([
            'status' => 'verified',
        ]);

        return redirect()->back()
            ->with('message', 'Pembayaran berhasil dikonfirmasi (lunas).')
            ->with('type', 'success');
    }
}
