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
    public function index()
    {
        $pasien = Auth::user();

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

    public function upload(Request $request, $id_periksa)
    {
        $request->validate(
            [
                'file_bukti' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            ],
            [
                'file_bukti.required' => 'Bukti pembayaran wajib diupload.',
                'file_bukti.file' => 'File bukti pembayaran tidak valid.',
                'file_bukti.mimes' => 'Format bukti pembayaran harus JPG, JPEG, PNG, atau WEBP.',
                'file_bukti.max' => 'Ukuran bukti pembayaran maksimal 5 MB.',
                'file_bukti.uploaded' => 'File bukti gagal diupload. Pastikan ukuran file tidak terlalu besar dan format file sesuai.',
            ]
        );

        $daftar = DaftarPoli::where('id_pasien', Auth::id())
            ->whereHas('periksa', function ($query) use ($id_periksa) {
                $query->where('id', $id_periksa);
            })
            ->with('periksa.buktiPembayaran')
            ->firstOrFail();

        $periksa = $daftar->periksa;
        $bukti = $periksa->buktiPembayaran;

        if ($bukti && $bukti->isVerified()) {
            return redirect()
                ->back()
                ->with('message', 'Pembayaran sudah diverifikasi, bukti tidak dapat diupload ulang.')
                ->with('type', 'error');
        }

        if (!$request->hasFile('file_bukti')) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'file_bukti' => 'File bukti pembayaran belum dipilih.',
                ]);
        }

        $file = $request->file('file_bukti');

        if (!$file->isValid()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'file_bukti' => 'File bukti pembayaran gagal diupload. Coba gunakan file JPG/PNG dengan ukuran lebih kecil.',
                ]);
        }

        $path = $file->store('bukti-pembayaran', 'public');

        if ($bukti && $bukti->file_bukti) {
            Storage::disk('public')->delete($bukti->file_bukti);
        }

        if ($bukti) {
            $bukti->update([
                'file_bukti' => $path,
                'status' => 'pending',
            ]);
        } else {
            BuktiPembayaran::create([
                'id_periksa' => $id_periksa,
                'file_bukti' => $path,
                'status' => 'pending',
            ]);
        }

        return redirect()
            ->back()
            ->with('message', 'Bukti pembayaran berhasil diupload. Silakan tunggu verifikasi dari admin.')
            ->with('type', 'success');
    }
}