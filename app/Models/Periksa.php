<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periksa extends Model
{
    protected $table = 'periksa';

    protected $fillable = [
        'id_daftar_poli',
        'tgl_periksa',
        'catatan',
        'biaya_periksa',
    ];

    /**
     * Periksa milik satu DaftarPoli.
     */
    public function daftarPoli()
    {
        return $this->belongsTo(DaftarPoli::class, 'id_daftar_poli');
    }

    /**
     * Satu Periksa punya banyak DetailPeriksa (obat-obatan yang diresepkan).
     */
    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_periksa');
    }

    /**
     * Satu Periksa punya satu BuktiPembayaran (upload foto bukti bayar pasien).
     */
    public function buktiPembayaran()
    {
        return $this->hasOne(BuktiPembayaran::class, 'id_periksa');
    }

    /**
     * Hitung total biaya: biaya_periksa + jumlah harga semua obat yang diresepkan.
     * Dipakai di halaman riwayat & tagihan pasien.
     */
    public function getTotalBiayaAttribute(): int
    {
        $biayaObat = $this->detailPeriksas->sum(function ($detail) {
            return $detail->obat->harga ?? 0;
        });

        return $this->biaya_periksa + $biayaObat;
    }
}
