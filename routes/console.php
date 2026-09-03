<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

/*Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');*/

/*
|--------------------------------------------------------------------------
| Queue worker (piggybacks on the Laravel scheduler)
|--------------------------------------------------------------------------
|
| QUEUE_CONNECTION=database, but nothing has ever actually processed that
| queue — every mail/notification marked ShouldQueue (interest emails,
| profile verified/rejected, ...) just accumulates in `jobs` forever and is
| never sent. No persistent worker process (Supervisor/systemd) is assumed
| to be available on this host, so instead of requiring one, this runs a
| short-lived worker every minute via the scheduler, which only needs ONE
| cron line on the server (see README/deployment notes):
|
|   * * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
|
| --stop-when-empty makes it exit as soon as the queue is drained (instead
| of idling), and --max-time=50 caps a single run so it can't still be
| going when the next minute's scheduler tick fires.
| withoutOverlapping() is a second safety net for the same reason.
*/
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
