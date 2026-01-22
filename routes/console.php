<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('interviews:send-reminders')
    ->dailyAt('09:00')
    ->timezone('Europe/Paris')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/interview-reminders.log'))
    ->description('Rappels d\'entretien (TEST)');

Schedule::command('applications:check-followups')
    ->dailyAt('12:10')
    ->timezone('Europe/Paris') 
    ->onSuccess(function () {
        info('✅ Vérification des relances effectuée avec succès');
    })
    ->onFailure(function () {
        error('❌ Erreur lors de la vérification des relances');
    });