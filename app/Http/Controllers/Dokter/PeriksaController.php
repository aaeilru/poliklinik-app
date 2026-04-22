<?php

namespace App\Http\Controllers\Dokter;

use App\Events\QueueUpdated;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriksaController extends Controller
{
    public function index()
    {
        $dokter = Auth::user();
        $daftarPasien = DaftarPoli::whereHas('jadwalPeriksa', fn($q) => $q->where('id_dokter', $dokter->id))
            ->whereDoesntHave('periksas')
            ->with(['pasien', 'jadwalPeriksa'])
            ->orderBy('no_antrian')
            ->get();
        $obatHabis = Obat::where('stok', '<=', 0)->count();
        return view('dokter.periksa.index', compact('daftarPasien', 'obatHabis'));
    }

    public function create($id_daftar)
    {
        $daftar = DaftarPoli::with(['pasien', 'jadwalPeriksa'])->findOrFail($id_daftar);
        abort_if($daftar->jadwalPeriksa->id_dokter !== Auth::id(), 403);
        if ($daftar->sudahDiperiksa()) {
            return redirect()->route('periksa.index')->with('message','Pasien ini sudah diperiksa.')->with('type','error');
        }
        $obats = Obat::where('stok', '>', 0)->orderBy('nama_obat')->get();
        return view('dokter.periksa.create', compact('daftar', 'obats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'catatan'        => 'nullable|string',
            'biaya_periksa'  => 'required|integer|min:0',
            'obat_ids'       => 'nullable|array',
            'obat_ids.*'     => 'exists:obat,id',
        ]);

        DB::beginTransaction();
        try {
            // STEP 1: Cek stok semua obat dulu (lockForUpdate cegah race condition)
            if (!empty($request->obat_ids)) {
                $obats = Obat::whereIn('id', $request->obat_ids)->lockForUpdate()->get();
                foreach ($obats as $obat) {
                    if ($obat->stok <= 0) {
                        DB::rollBack();
                        return redirect()->back()->withInput()
                            ->with('message', "❌ Stok obat \"{$obat->nama_obat}\" sudah habis! Hapus dari resep.")
                            ->with('type', 'error');
                    }
                }
            }

            // STEP 2: Simpan Periksa
            $periksa = Periksa::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'tgl_periksa'    => now(),
                'catatan'        => $request->catatan,
                'biaya_periksa'  => $request->biaya_periksa,
            ]);

            // STEP 3: Simpan detail obat + kurangi stok
            if (!empty($request->obat_ids)) {
                foreach ($request->obat_ids as $idObat) {
                    DetailPeriksa::create(['id_periksa' => $periksa->id, 'id_obat' => $idObat]);
                    Obat::where('id', $idObat)->decrement('stok');
                }
            }

            // STEP 4: Buat record BuktiPembayaran awal
            $periksa->buktiPembayaran()->create(['status' => 'pending']);

            DB::commit();

            // STEP 5: Broadcast WebSocket ke semua client dashboard
            $daftar        = DaftarPoli::find($request->id_daftar_poli);
            $nomorDilayani = DaftarPoli::where('id_jadwal', $daftar->id_jadwal)
                ->whereHas('periksas')->max('no_antrian') ?? 0;
            broadcast(new QueueUpdated($daftar->id_jadwal, $nomorDilayani));

            return redirect()->route('periksa.index')->with('message','Hasil periksa berhasil disimpan!')->with('type','success');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('message', 'Terjadi kesalahan: ' . $e->getMessage())
                ->with('type', 'error');
        }
    }

    public function riwayat()
    {
        $dokter  = Auth::user();
        $riwayat = DaftarPoli::whereHas('jadwalPeriksa', fn($q) => $q->where('id_dokter', $dokter->id))
            ->whereHas('periksas')
            ->with(['pasien','jadwalPeriksa','periksa.detailPeriksas.obat','periksa.buktiPembayaran'])
            ->orderByDesc('updated_at')->get();
        return view('dokter.periksa.riwayat', compact('riwayat'));
    }
}
