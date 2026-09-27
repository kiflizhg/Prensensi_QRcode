<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_pages', function (Blueprint $table) {
            $table->id();
            $table->string('quote', 500);
            $table->string('title');
            $table->text('description');
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('guide');
            $table->string('principal_name')->nullable();
            $table->text('principal_bio')->nullable();
            $table->string('principal_photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_pages');
    }
};
