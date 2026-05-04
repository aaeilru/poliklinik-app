<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event ini di-broadcast ke semua browser yang sedang buka dashboard pasien.
 *
 * ALUR LENGKAP:
 * 1. Dokter klik "Simpan Hasil Periksa"
 * 2. ::store() dispatch event ini
 * 3. Laravel Reverb (WebSocket server) terima event
 * 4. Reverb kirim ke semua browser yang subscribe channel "antrian"
 * 5. Laravel Echo di browser pasien tangkap event
 * 6. JavaScript update angka nomor dilayani TANPA reload halaman
 */
class QueueUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    /**
     * @param int $idJadwal      ID jadwal yang antrianya berubah
     * @param int $nomorDilayani Nomor antrian yang baru saja selesai diperiksa
     */
    public function __construct(
        public int $idJadwal,
        public int $nomorDilayani,
    ) {}

    /**
     * Channel tempat event di-broadcast.
     * Pakai Channel biasa (public) → siapapun bisa dengar tanpa auth.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('antrian'),
        ];
    }

    /**
     * Nama event yang didengarkan Laravel Echo di frontend.
     * Titik di depan (.queue.updated) = custom event name di Echo.
     */
    public function broadcastAs(): string
    {
        return 'queue.updated';
    }

    /**
     * Data yang dikirim ke browser.
     * Di JavaScript: event.id_jadwal dan event.no_dilayani
     */
    public function broadcastWith(): array
    {
        return [
            'id_jadwal'   => $this->idJadwal,
            'no_dilayani' => $this->nomorDilayani,
        ];
    }
}