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
Schedule::command('peer-lend:calculate-penalties')
    ->daily()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/calculate-penalties.log'));

// Send repayment reminders for installments due in 3 days — runs every day
Schedule::command(\App\Console\Commands\SendRepaymentRemindersCommand::class)
    ->daily()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/repayment-reminders.log'));

// 🤖 Auto-Invest Engine (Every Hour matching & auto-funding)
Schedule::command('peer-lend:run-auto-invest')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/auto-invest.log'));
