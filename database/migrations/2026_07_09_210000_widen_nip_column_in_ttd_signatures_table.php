<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Melebarkan kolom `nip` dari varchar(18) ke varchar(50) — pembatasan
 * "NIP wajib 18 digit" sudah dihapus dari validasi (UpdateTtdRequest),
 * tapi kolomnya sendiri sudah pernah dibuat & dimigrasikan dengan panjang
 * lama (18) sebelum perubahan itu, jadi perlu migration ALTER terpisah
 * (edit langsung ke migration create sebelumnya tidak berpengaruh ke
 * database yang sudah pernah di-migrate). Pakai raw SQL, bukan
 * Schema::table()->change(), karena doctrine/dbal tidak terpasang di
 * proyek ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('ttd_signatures', 'nip')) {
            DB::statement('ALTER TABLE ttd_signatures MODIFY nip VARCHAR(50) NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ttd_signatures', 'nip')) {
            DB::statement('ALTER TABLE ttd_signatures MODIFY nip VARCHAR(18) NOT NULL');
        }
    }
};
