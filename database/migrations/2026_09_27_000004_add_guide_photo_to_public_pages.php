<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_pages', function (Blueprint $table) {
            $table->string('guide_photo_path')->nullable();
            $table->string('guide_caption', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('public_pages', fn (Blueprint $table) => $table->dropColumn(['guide_photo_path', 'guide_caption']));
    }
};
