<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Riwayat monitoring otomatis: ambil snapshot dari Firebase tiap 10 menit.
// Jalankan dengan: php artisan schedule:work
Schedule::command('firebase:sync')->everyTenMinutes()->withoutOverlapping();
