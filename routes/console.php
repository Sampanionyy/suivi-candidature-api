<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('interviews:send-reminders')
    ->dailyAt('09:00') // Heure fixe
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/interview-reminders.log'))
    ->description('📧 Rappels d\'entretien (TEST)');