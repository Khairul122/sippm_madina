<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mendukung "batal disposisi" — Kominfo salah memilih target OPD/Camat
 * saat disposisi bisa membatalkannya (selama status pengaduan masih
 * DIPROSES, sebelum OPD/Camat mengirim penanganan) supaya bisa
 * didisposisikan ulang dengan target yang benar. Baris disposisi TIDAK
 * dihapus (soft-cancel via kolom, bukan delete()) supaya riwayat tetap
 * lengkap untuk audit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('note');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            $table->string('cancel_note')->nullable()->after('cancelled_by');
        });
    }

    public function down(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropColumn(['cancelled_at', 'cancel_note']);
        });
    }
};
