<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ttd_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penandatangan');
            $table->string('jabatan_penandatangan');
            $table->string('pangkat')->nullable();
            $table->string('nip', 32);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ttd_signatures');
    }
};
