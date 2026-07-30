<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu baris konfigurasi tampilan publik aktif (selalu diakses/diupdate
 * lewat updateOrCreate(['id' => 1], ...) di controller) — pola sama
 * persis dengan manual_books & ttd_signatures. Menyimpan foto hero
 * beranda (mis. foto Bupati aktif) yang bisa diganti Kominfo lewat
 * dashboard tanpa deploy ulang, supaya gampang diganti saat periode
 * jabatan berakhir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image_path')->nullable();
            $table->string('hero_caption')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
