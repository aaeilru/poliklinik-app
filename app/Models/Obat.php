<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'nama_obat',
        'kemasan',
        'harga',
        'stok',         
    ];

    /**
     * Batas minimum stok untuk indikator visual "hampir habis".
     * Jika stok <= 10 maka ditampilkan badge kuning/amber.
     */
    const STOK_MINIMUM = 10;

    /**
     * Cek apakah stok sudah benar-benar habis (= 0).
     * Dipakai di view untuk menampilkan badge merah.
     */
    public function isHabis(): bool
    {
        return $this->stok <= 0;
    }

    /**
     * Cek apakah stok hampir habis (1 s/d STOK_MINIMUM).
     * Dipakai di view untuk menampilkan badge kuning.
     */
    public function isRendah(): bool
    {
        return $this->stok > 0 && $this->stok <= self::STOK_MINIMUM;
    }

    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_obat');
    }
}
