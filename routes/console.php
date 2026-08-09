<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

use App\Infrastructure\Persistence\Eloquent\Models\SiteSetting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NFR-13/FR-39: "Basis data dicadangkan secara otomatis berdasarkan konfigurasi admin."
try {
    $setting = SiteSetting::query()->first();
    $frequency = $setting?->backup_frequency ?? 'weekly';
    $time = $setting?->backup_time ?? '01:00';

    $backupCmd = Schedule::command('backup:run --only-db')->onOneServer();
    $cleanCmd = Schedule::command('backup:clean')->onOneServer();

    $hour = explode(':', $time)[0] ?? '01';

    match ($frequency) {
        'daily' => $backupCmd->dailyAt($time),
        'every_12_hours' => $backupCmd->twiceDaily((int) $hour % 12, ((int) $hour % 12) + 12),
        'every_3_days' => $backupCmd->cron("0 {$hour} */3 * *"),
        'monthly' => $backupCmd->monthlyOn(1, $time),
        default => $backupCmd->weeklyOn(0, $time),
    };

    $cleanCmd->weeklyOn(0, '01:30');
} catch (\Throwable $e) {
    Schedule::command('backup:run --only-db')->weeklyOn(0, '01:00')->onOneServer();
    Schedule::command('backup:clean')->weeklyOn(0, '01:30')->onOneServer();
}
