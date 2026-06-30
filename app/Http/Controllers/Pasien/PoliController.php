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
        $request->validate(
            [
                'id_jadwal' => 'required|exists:jadwal_periksa,id',
                'keluhan' => 'required|string|min:3|max:255',
            ],
            [
                'id_jadwal.required' => 'Jadwal periksa wajib dipilih.',
                'id_jadwal.exists' => 'Jadwal periksa tidak valid.',
                'keluhan.required' => 'Keluhan wajib diisi.',
                'keluhan.string' => 'Keluhan harus berupa teks.',
                'keluhan.min' => 'Keluhan minimal 3 karakter.',
                'keluhan.max' => 'Keluhan maksimal 255 karakter.',
            ]
        );

        $pasienId = auth()->id();

        $sudahDaftar = \App\Models\DaftarPoli::where('id_pasien', $pasienId)
            ->where('id_jadwal', $request->id_jadwal)
            ->exists();

        if ($sudahDaftar) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message', 'Anda sudah mendaftar pada jadwal poli ini.')
                ->with('type', 'error');
        }

        $jumlahSudahDaftar = \App\Models\DaftarPoli::where('id_jadwal', $request->id_jadwal)
            ->count();

        \App\Models\DaftarPoli::create([
            'id_pasien' => $pasienId,
            'id_jadwal' => $request->id_jadwal,
            'keluhan' => $request->keluhan,
            'no_antrian' => $jumlahSudahDaftar + 1,
        ]);

        return redirect()
            ->route('pasien.dashboard')
            ->with('message', 'Berhasil mendaftar ke Poli! Silakan cek nomor antrian Anda di dashboard.')
            ->with('type', 'success');
    }
}
