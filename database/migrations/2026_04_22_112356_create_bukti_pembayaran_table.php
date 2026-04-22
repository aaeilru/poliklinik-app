<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_pembayaran', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel periksa — 1 pemeriksaan punya 1 tagihan
            $table->foreignId('id_periksa')
                  ->constrained('periksa')
                  ->cascadeOnDelete();

            // Path file foto bukti yang diupload pasien
            // Disimpan di storage/app/public/bukti/
            $table->string('file_bukti')->nullable();

            // Status alur pembayaran:
            // pending  = belum upload atau menunggu verifikasi admin
            // verified = sudah dikonfirmasi admin (lunas)
            $table->enum('status', ['pending', 'verified'])->default('pending');

            // Catatan opsional dari admin saat verifikasi
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_pembayaran');
    }
};