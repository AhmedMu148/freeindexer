<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
  $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:daily-cron')
  ->dailyAt('02:30')
  ->timezone('UTC')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/daily-cron.log'));

Schedule::command('app:reset-flag')
  ->dailyAt('02:00')
  ->timezone('UTC')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/daily-cron.log'));

// Schedule::command('warehouse:move-old-to-temp')
//   ->everyMinute()
//   ->withoutOverlapping()
//   ->appendOutputTo(storage_path('logs/daily-cron.log'));

Schedule::command('warehouse:upload-temp-to-s3')
  ->everyMinute()
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/daily-cron.log'));

Schedule::command('warehouse:clean-old')
  ->everyMinute()
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/daily-cron.log'));
