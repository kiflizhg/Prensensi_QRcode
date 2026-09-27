<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('presensi_qr_codes')) {
            return;
        }

        // QR yang digunakan aplikasi tersimpan pada gurus.token_qr.
        // Hentikan migrasi jika instalasi lain masih memiliki data QR lama.
        if (DB::table('presensi_qr_codes')->exists()) {
            throw new RuntimeException('Tabel QR lama masih berisi data. Arsipkan data sebelum menjalankan pembersihan.');
        }

        Schema::drop('presensi_qr_codes');
    }

    public function down(): void
    {
        if (! Schema::hasTable('presensi_qr_codes')) {
            $original = require __DIR__.'/2026_06_01_192332_create_presensi_qr_codes_table.php';
            $original->up();
        }
    }
};
