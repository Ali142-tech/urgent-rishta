<?php

namespace App\Console\Commands;

use App\Services\InactivityReminderService;
use Illuminate\Console\Command;

/**
 * Runs automatically once a day (see App\Console\Kernel::schedule) — not
 * meant to be run manually like the profile-completion campaign, since this
 * check needs to happen continuously/automatically rather than as a
 * one-time admin-triggered blast.
 *
 *   php artisan reminders:inactivity --dry-run   # preview count, nothing sent
 *   php artisan reminders:inactivity              # dispatch reminders for everyone currently eligible
 */
class SendInactivityReminders extends Command
{
    protected $signature = 'reminders:inactivity {--dry-run : Show recipient count without sending anything}';

    protected $description = 'Email members inactive 7+ days a "we miss you" reminder';

    public function handle(InactivityReminderService $service)
    {
        $total = (clone $service->eligibleUsersQuery())->count();
        $this->info("Eligible for inactivity reminder: {$total}");

        if ($this->option('dry-run')) {
            $this->line('--- DRY RUN — nothing sent. Sample recipients: ---');
            foreach ((clone $service->eligibleUsersQuery())->take(5)->get() as $u) {
                $this->line(" - {$u->dataid}  {$u->first_name} {$u->last_name}  <{$u->email}>  last_login_at: " . ($u->last_login_at ?: 'never'));
            }
            return 0;
        }

        if ($total === 0) {
            $this->info('Nobody currently eligible.');
            return 0;
        }

        $dispatched = $service->dispatchEligible();
        $this->info("Dispatched {$dispatched} inactivity reminders.");
        return 0;
    }
}
