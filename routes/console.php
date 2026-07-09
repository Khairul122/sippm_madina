<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NFR-13/FR-39: "Basis data dicadangkan setiap hari secara otomatis."
// spatie/laravel-backup was already installed (composer.json) but never
// actually scheduled — this is the missing piece. Requires the Laravel
// scheduler cron entry to be running (`* * * * * php artisan schedule:run`)
// in production; in this local dev environment it only fires while
// `php artisan schedule:work` is running.
Schedule::command('backup:run')->daily()->at('01:00')->onOneServer();
Schedule::command('backup:clean')->daily()->at('01:30')->onOneServer();
