<?php

use App\Console\Commands\CalculatePenaltiesCommand;
use App\Console\Commands\UpdateCryptoLtvCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Peer-Lend Scheduled Jobs ────────────────────────────────────────────────

// Recalculate crypto LTV and auto-liquidate overdue collateral — runs every hour
Schedule::command(UpdateCryptoLtvCommand::class)
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/ltv-update.log'));

// Calculate daily penalties and flag overdue installments — runs every day
Schedule::command(CalculatePenaltiesCommand::class)
    ->daily()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/calculate-penalties.log'));


