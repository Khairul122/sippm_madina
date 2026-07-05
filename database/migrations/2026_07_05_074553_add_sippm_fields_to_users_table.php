<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable: internal (non-masyarakat) users may not have a NIK.
            $table->string('nik')->nullable()->unique()->after('email');
            $table->string('phone')->nullable()->unique()->after('nik');
            $table->boolean('is_active')->default(true)->after('phone');
            // UU PDP consent timestamp, collected at registration.
            $table->timestamp('consent_at')->nullable()->after('is_active');
            $table->foreignId('opd_id')->nullable()->after('consent_at')
                ->constrained('opds')->nullOnDelete();
            $table->foreignId('kecamatan_id')->nullable()->after('opd_id')
                ->constrained('kecamatans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opd_id');
            $table->dropConstrainedForeignId('kecamatan_id');
            $table->dropColumn(['nik', 'phone', 'is_active', 'consent_at']);
        });
    }
};
