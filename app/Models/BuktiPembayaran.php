<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPembayaran extends Model
{
    protected $table = 'bukti_pembayaran';

    protected $fillable = [
        'id_periksa',
        'file_bukti',
        'status',
        'catatan_admin',
    ];

    /**
     * Satu BuktiPembayaran milik satu Periksa.
     */
    public function periksa()
    {
        return $this->belongsTo(Periksa::class, 'id_periksa');
    }

    /**
     * Helper: cek apakah pembayaran sudah diverifikasi admin.
     * Dipakai di view untuk tampilkan badge "Lunas".
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }
}