<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyQrCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_legacy_table_is_removed_and_rollback_restores_schema(): void
    {
        $migration = require database_path('migrations/2026_09_25_000001_remove_unused_presensi_qr_codes_table.php');
        $this->assertFalse(Schema::hasTable('presensi_qr_codes'));
        $migration->down();
        $this->assertTrue(Schema::hasTable('presensi_qr_codes'));
        $migration->up();
        $this->assertFalse(Schema::hasTable('presensi_qr_codes'));
        $this->assertTrue(Schema::hasColumn('gurus', 'token_qr'));
    }

    public function test_cleanup_refuses_to_delete_legacy_records(): void
    {
        $migration = require database_path('migrations/2026_09_25_000001_remove_unused_presensi_qr_codes_table.php');
        $migration->down();
        DB::table('presensi_qr_codes')->insert(['token' => 'legacy-test', 'tanggal' => '2026-09-25']);
        try {
            $migration->up();
            $this->fail('Pembersihan harus menolak tabel yang berisi data.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('masih berisi data', $exception->getMessage());
        }
        $this->assertDatabaseHas('presensi_qr_codes', ['token' => 'legacy-test']);
    }
}
