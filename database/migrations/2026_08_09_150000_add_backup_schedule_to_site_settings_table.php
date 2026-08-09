<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('backup_frequency')->default('weekly')->after('hero_caption');
            $table->string('backup_time')->default('01:00')->after('backup_frequency');
            $table->timestamp('last_manual_backup_at')->nullable()->after('backup_time');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['backup_frequency', 'backup_time', 'last_manual_backup_at']);
        });
    }
};
