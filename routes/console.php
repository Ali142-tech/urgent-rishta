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

/*
|--------------------------------------------------------------------------
| Scheduled email jobs
|--------------------------------------------------------------------------
|
| Moved here from app/Console/Kernel::schedule() — that Kernel class is
| never instantiated by this app (see bootstrap/app.php: Laravel 12's
| ->withRouting(commands: 'routes/console.php') is what actually wires up
| the scheduler, the same "new bootstrap-based registration, not the old
| Kernel class" gotcha already hit once before with middleware aliases in
| bootstrap/app.php vs. the unused app/Http/Kernel.php). Kernel.php's
| schedule() method is left empty on purpose — do not add entries there,
| they will silently never run.
*/

// Checks daily for members inactive 7+ days and emails them a reminder —
// see App\Console\Commands\SendInactivityReminders.
Schedule::command('reminders:inactivity')->dailyAt('10:00')->withoutOverlapping();

// Weekly "match preview" email — see App\Console\Commands\SendMatchPreviewEmails.
Schedule::command('matches:send-preview')->weeklyOn(1, '09:00')->withoutOverlapping();

// Daily check for members due their personalized weekly match email (7+
// days since last sent, or never sent) — see
// App\Console\Commands\SendWeeklyMatches / App\Services\WeeklyMatchService.
Schedule::command('matches:send-weekly')->dailyAt('09:30')->withoutOverlapping();
