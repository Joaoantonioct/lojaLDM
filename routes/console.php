<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::call(fn () => app(\App\Services\PublicationInventoryService::class)->removeUnavailable())->name("cleanup-publications")->everyMinute()->withoutOverlapping();
