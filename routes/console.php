<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NFR-13/FR-39: "Basis data dicadangkan setiap minggu secara otomatis."
// spatie/laravel-backup is scheduled to run every Sunday at 01:00 WIB.
// Requires the Laravel scheduler cron entry (`* * * * * php artisan schedule:run`)
// in production; in local environment it fires while `php artisan schedule:work` is running.
Schedule::command('backup:run --only-db')->weeklyOn(0, '01:00')->onOneServer();
Schedule::command('backup:clean')->weeklyOn(0, '01:30')->onOneServer();
