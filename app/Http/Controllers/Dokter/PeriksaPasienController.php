<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        // Obat tetap ditampilkan semua agar dokter bisa melihat stoknya.
        // Obat dengan stok 0 akan dibuat disabled di bagian view.
        $obats = Obat::orderBy('nama_obat')->get();

        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'obat_json' => 'required|json',
            'catatan' => 'nullable|string',
            'biaya_periksa' => 'required|integer|min:0',
        ]);

        $obatIds = json_decode($request->obat_json, true);

        if (!is_array($obatIds) || count($obatIds) === 0) {
            throw ValidationException::withMessages([
                'obat_json' => 'Minimal pilih satu obat untuk resep pasien.',
            ]);
        }

        // Bersihkan ID obat agar data yang diproses benar-benar ID numerik.
        $obatIds = array_values(array_filter(array_map('intval', $obatIds), function ($id) {
            return $id > 0;
        }));

        if (count($obatIds) === 0) {
            throw ValidationException::withMessages([
                'obat_json' => 'Data obat tidak valid. Silakan pilih obat ulang.',
            ]);
        }

        // Hitung kebutuhan setiap obat.
        // Saat ini 1 obat dipilih = stok berkurang 1.
        $jumlahPerObat = array_count_values($obatIds);

        DB::transaction(function () use ($request, $obatIds, $jumlahPerObat) {
            $obats = Obat::whereIn('id', array_keys($jumlahPerObat))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($obats->count() !== count($jumlahPerObat)) {
                throw ValidationException::withMessages([
                    'obat_json' => 'Ada data obat yang tidak ditemukan. Silakan pilih obat ulang.',
                ]);
            }

            // Validasi stok sebelum data periksa disimpan.
            foreach ($jumlahPerObat as $idObat => $jumlahDibutuhkan) {
                $obat = $obats->get($idObat);

                if ($obat->stok < $jumlahDibutuhkan) {
                    throw ValidationException::withMessages([
                        'obat_json' => "Stok obat {$obat->nama_obat} tidak mencukupi. Stok tersedia: {$obat->stok}.",
                    ]);
                }
            }

            // Simpan data periksa.
            $periksa = Periksa::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'tgl_periksa' => now(),
                'catatan' => $request->catatan,
                'biaya_periksa' => $request->biaya_periksa + 150000,
            ]);

            // Simpan detail obat resep.
            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $idObat,
                ]);
            }

            // Kurangi stok otomatis setelah resep berhasil disimpan.
            foreach ($jumlahPerObat as $idObat => $jumlahDibutuhkan) {
                $obats->get($idObat)->decrement('stok', $jumlahDibutuhkan);
            }
        });

        return redirect()->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan dan stok obat berhasil diperbarui.');
    }
}