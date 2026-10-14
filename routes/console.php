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
| Queue worker
|--------------------------------------------------------------------------
|
| The queue worker is NOT started from here. On the production server it has its own cron job (every minute):
|
|   /usr/bin/php /home/.../public_html/artisan queue:work --queue=campaigns,default --stop-when-empty --max-time=55 --tries=6
|
| The scheduler below needs its own, separate cron job (every minute):
|
|   /usr/bin/php /home/.../public_html/artisan schedule:run
*/

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
// The ONE weekly e-mail: every member gets the three newest profiles. This only queues the jobs (a minute or two);
// the every-minute queue worker above sends them.
Schedule::command('matches:send-preview')->weeklyOn(1, '09:00')->withoutOverlapping();

// (The personalized weekly match e-mail — matches:send-weekly — is no longer scheduled; matches:send-preview replaces it.)
