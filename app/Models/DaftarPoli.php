<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarPoli extends Model
{
    protected $table = 'daftar_poli';

    protected $fillable = [
        'id_jadwal',
        'id_pasien',
        'keluhan',
        'no_antrian',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'id_pasien');
    }

    public function jadwalPeriksa()
    {
        return $this->belongsTo(JadwalPeriksa::class, 'id_jadwal');
    }

    /**
     * Satu pendaftaran bisa menghasilkan satu atau lebih periksa.
     * Normalnya hanya satu (dipakai cek sudah diperiksa atau belum).
     */
    public function periksas()
    {
        return $this->hasMany(Periksa::class, 'id_daftar_poli');
    }

    /**
     * Shortcut: ambil data periksa pertama (hasil pemeriksaan).
     */
    public function periksa()
    {
        return $this->hasOne(Periksa::class, 'id_daftar_poli');
    }

    /**
     * Cek apakah pasien ini sudah selesai diperiksa.
     * Dipakai untuk constraint "tidak bisa daftar lagi sebelum selesai diperiksa".
     */
    public function sudahDiperiksa(): bool
    {
        return $this->periksas()->exists();
    }
}
