<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_pages', function (Blueprint $table) {
            $table->string('whatsapp_url', 2048)->nullable();
            $table->string('instagram_url', 2048)->nullable();
            $table->string('website_url', 2048)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('public_pages', fn (Blueprint $table) => $table->dropColumn(['whatsapp_url', 'instagram_url', 'website_url']));
    }
};
