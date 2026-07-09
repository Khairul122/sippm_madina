<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu file manual book aktif (selalu diakses/diupdate lewat
 * updateOrCreate(['id' => 1], ...) di controller) — pola sama persis
 * dengan ttd_signatures.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_books', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('original_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_books');
    }
};
