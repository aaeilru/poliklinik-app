<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\DaftarPoli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Daftar semua tagihan pasien yang sedang login.
     * Hanya menampilkan pendaftaran yang SUDAH diperiksa (sudah ada tagihan).
     */
    public function index()
    {
        $pasien = Auth::user();

        // Ambil semua pemeriksaan pasien ini (yang sudah ada record Periksa)
        $tagihans = DaftarPoli::where('id_pasien', $pasien->id)
            ->whereHas('periksas')
            ->with([
                'jadwalPeriksa.dokter.poli',
                'periksa.detailPeriksas.obat',
                'periksa.buktiPembayaran',
            ])
            ->orderByDesc('updated_at')
            ->get();

        return view('pasien.pembayaran.index', compact('tagihans'));
    }

    /**
     * Upload foto bukti pembayaran.
     * Pasien mengupload gambar, lalu admin akan verifikasi.
     */
    public function upload(Request $request, $id_periksa)
    {
        $request->validate([
            // File wajib ada, harus gambar, max 2MB
            'file_bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Pastikan periksa ini milik pasien yang login (keamanan)
        $daftar = DaftarPoli::where('id_pasien', Auth::id())
            ->whereHas('periksa', function ($q) use ($id_periksa) {
                $q->where('id', $id_periksa);
            })
            ->with('periksa.buktiPembayaran')
            ->firstOrFail();

        $bukti = $daftar->periksa->buktiPembayaran;

        // Jika sudah verified, tidak bisa upload ulang
        if ($bukti && $bukti->isVerified()) {
            return redirect()->back()
                ->with('message', 'Pembayaran sudah diverifikasi, tidak perlu upload ulang.')
                ->with('type', 'error');
        }

        // ── Simpan file ke storage/app/public/bukti ──────────────────────────
        // File diakses via /storage/bukti/namafile.jpg setelah php artisan storage:link
        $path = $request->file('file_bukti')->store('bukti', 'public');

        // Hapus file lama jika ada (re-upload)
        if ($bukti && $bukti->file_bukti) {
            Storage::disk('public')->delete($bukti->file_bukti);
        }

        // Update atau buat record BuktiPembayaran
        if ($bukti) {
            $bukti->update([
                'file_bukti' => $path,
                'status'     => 'pending', // reset ke pending jika re-upload
            ]);
        } else {
            BuktiPembayaran::create([
                'id_periksa' => $id_periksa,
                'file_bukti' => $path,
                'status'     => 'pending',
            ]);
        }

        return redirect()->back()
            ->with('message', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.')
            ->with('type', 'success');
    }
}
