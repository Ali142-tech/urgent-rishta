<?php

namespace App\Console\Commands;

use App\Mail\ProfileFreeWeekOffer;
use App\Services\FreeWeekCampaignService;
use App\Services\ProfileWizard;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * One-time "complete your profile → 1 week free access" email, sent ONLY to members whose profile is not 100% complete.
 *
 *   php artisan campaign:send-profile-free-week --test=you@example.com [--as=DATAID]   # real send to ONE address (sample list, or that member's real missing list)
 *   php artisan campaign:send-profile-free-week --dry-run                              # how many members would get it
 *   php artisan campaign:send-profile-free-week --limit=300                            # queue a first batch
 *   php artisan campaign:send-profile-free-week                                        # queue everyone not yet sent
 *   php artisan campaign:send-profile-free-week --status
 *
 * Only DISPATCHES jobs (queue "campaigns"); a queue worker does the sending. Re-running never emails the same member twice.
 */
class SendProfileFreeWeekCampaign extends Command
{
    protected $signature = 'campaign:send-profile-free-week
                            {--dry-run : Count the recipients, send nothing}
                            {--status : Show progress and exit}
                            {--limit= : Only queue this many members}
                            {--test= : Send one real email to this address only}
                            {--as= : With --test, use the real missing-fields list of this member (dataid)}';

    protected $description = 'Queue the "complete your profile, get 1 week free access" email for members whose profile is not 100% complete';

    public function handle(FreeWeekCampaignService $service, ProfileWizard $wizard)
    {
        if ($email = $this->option('test')) {
            $percent = 60;
            $missing = [
                ['title' => 'Personal Details', 'label' => 'Height'],
                ['title' => 'Personal Details', 'label' => 'Marital status'],
                ['title' => 'Education & Career', 'label' => 'Profession'],
            ];
            if ($as = $this->option('as')) {
                $user = User::where('dataid', $as)->first();
                if (!$user) {
                    $this->error("No member with dataid {$as}.");
                    return 1;
                }
                $st = $wizard->status($user);
                $percent = $st['percent'];
                $missing = $st['missing'];
            }
            try {
                Mail::to($email)->send(new ProfileFreeWeekOffer('there', $percent, $missing));
                $this->info("Test email sent to {$email}.");
                return 0;
            } catch (\Throwable $e) {
                $this->error('Test send failed: ' . $e->getMessage());
                return 1;
            }
        }

        if ($this->option('status')) {
            $s = $service->status();
            $this->table(['Sent', 'Queued', 'Failed'], [[$s['sent'], $s['queued'], $s['failed']]]);
            return 0;
        }

        $this->info('Counting members with an incomplete profile (this can take a minute)...');
        $total = $service->countIncomplete();
        $this->info("Members with an incomplete profile who have not been sent this email: {$total}");

        if ($this->option('dry-run')) {
            return 0;
        }
        if ($total === 0) {
            $this->info('Nothing to send.');
            return 0;
        }

        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $will = $limit ? min($limit, $total) : $total;
        if (!$this->confirm("This will queue {$will} emails. Continue?")) {
            $this->line('Cancelled.');
            return 0;
        }

        $n = $service->dispatchEligible($limit);
        $this->info("Queued {$n} emails. Make sure the queue worker is running (queue:work --queue=campaigns,default). Progress: --status");
        return 0;
    }
}
