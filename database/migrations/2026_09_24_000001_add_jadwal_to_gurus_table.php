<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('gurus', 'jadwal')) {
            Schema::table('gurus', fn (Blueprint $table) => $table->json('jadwal')->nullable());
        }
        // Keep existing files as backups while importing schedules.
        DB::table('gurus')->whereNull('jadwal')->orderBy('id')->each(function ($guru) {
            $path = 'jadwal-guru/'.$guru->id.'.json';
            if (Storage::disk('local')->exists($path)) {
                $jadwal = json_decode(Storage::disk('local')->get($path), true, 512, JSON_THROW_ON_ERROR);
                DB::table('gurus')->where('id', $guru->id)->update(['jadwal' => json_encode($jadwal, JSON_THROW_ON_ERROR)]);
            }
        });
    }

    public function down(): void
    {
        DB::table('gurus')->whereNotNull('jadwal')->orderBy('id')->each(function ($guru) {
            if (! Storage::disk('local')->put('jadwal-guru/'.$guru->id.'.json', $guru->jadwal)) {
                throw new RuntimeException('Tidak dapat mencadangkan jadwal guru.');
            }
        });
        Schema::table('gurus', fn (Blueprint $table) => $table->dropColumn('jadwal'));
    }
};
