<?php

namespace App\Console\Commands;

use App\Jobs\SendMatchPreviewEmailJob;
use App\Mail\MatchPreview;
use App\Services\MatchPreviewService;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * The weekly e-mail: every active member gets the three newest profiles of the opposite gender.
 * The command queues one small job per member (SendMatchPreviewEmailJob), spaced under the SES send limit; the
 * every-minute queue worker in routes/console.php sends them.
 * Scheduled for Mondays 09:00 in routes/console.php.
 */
class SendMatchPreviewEmails extends Command
{
    protected $signature = 'matches:send-preview
                            {--dry-run : Show recipient count and the three profiles without sending anything}
                            {--test= : Send one real email to this address only, as a male recipient}';

    protected $description = 'Email every active member the three newest profiles of the opposite gender';

    private function eligibleUsersQuery()
    {
        return User::where('active', 1)
            ->where('admin', 0)
            ->whereNull('added_by')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereIn('gender', ['male', 'female']);
    }

    public function handle(MatchPreviewService $service)
    {
        if ($testEmail = $this->option('test')) {
            return $this->sendTest($testEmail, $service);
        }

        $total = (clone $this->eligibleUsersQuery())->count();
        $this->info("Members to e-mail: {$total}");

        // The same three profiles for everyone of a gender: built once per gender.
        $profilesFor = [
            'male' => $service->curatedProfilesFor('male'),
            'female' => $service->curatedProfilesFor('female'),
        ];
        foreach ($profilesFor as $gender => $profiles) {
            $this->line(ucfirst($gender) . ' members get: ' . ($profiles->pluck('dataid')->implode(', ') ?: '(nothing — skipped)'));
        }

        if ($this->option('dry-run')) {
            $this->line('--- DRY RUN — nothing sent ---');
            return 0;
        }

        // Fresh three profiles for this week's send (the jobs read them from the cache).
        foreach (['male', 'female'] as $gender) {
            \Illuminate\Support\Facades\Cache::forget('weekly_email_profiles_' . $gender);
        }

        // One queued job per member, ~10 a second apart (under the 14/second SES limit): the queue worker sends them
        // in the background and a failure of one member never stops the rest.
        $queued = 0;
        $this->eligibleUsersQuery()->orderBy('users.id')->chunkById(500, function ($users) use (&$queued, $profilesFor) {
            foreach ($users as $user) {
                if (($profilesFor[$user->gender] ?? collect())->isEmpty()) {
                    continue;
                }
                SendMatchPreviewEmailJob::dispatch($user->id, $user->email, $user->first_name ?: 'there', $user->gender)
                    ->delay(now()->addMilliseconds($queued * 100));
                $queued++;
            }
        }, 'users.id', 'id');

        $minutes = (int) ceil($queued / 600);
        $this->info("Queued {$queued} weekly e-mails — they send over about {$minutes} minute(s) as the queue worker runs.");
        Log::info("matches:send-preview — queued {$queued} job(s).");
        return 0;
    }

    private function sendTest(string $email, MatchPreviewService $service): int
    {
        try {
            $profiles = $service->curatedProfilesFor('male');
            Mail::to($email)->send(new MatchPreview('there', $profiles));
            $this->info("Test email sent to {$email}.");
            return 0;
        } catch (\Throwable $e) {
            $this->error('Test send failed: ' . $e->getMessage());
            return 1;
        }
    }
}