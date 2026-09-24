<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * NOT called by this app — App\Console\Kernel is never instantiated.
     * bootstrap/app.php uses Laravel 12's ->withRouting(commands:
     * 'routes/console.php') to wire up the scheduler directly, the same
     * "new bootstrap-based registration replaces the old Kernel class"
     * pattern already hit once before with HTTP middleware aliases (see
     * bootstrap/app.php vs. the likewise-unused app/Http/Kernel.php).
     * All schedule()->command(...) entries live in routes/console.php —
     * do not add any here, they will silently never run.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
