<?php

namespace App\Console\Commands;

use App\Jobs\SendMatchPreviewEmailJob;
use App\Mail\MatchPreview;
use App\Services\MatchPreviewService;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Runs automatically once a week (see App\Console\Kernel::schedule) — sends
 * every active member a preview of a few opposite-gender curated profiles
 * (blurred), reusing the exact list already set up for the homepage.
 *
 *   php artisan matches:send-preview --test=you@example.com   # real send to ONE address only
 *   php artisan matches:send-preview --dry-run                # preview recipient count, nothing sent
 *   php artisan matches:send-preview                           # dispatch for everyone eligible
 */
class SendMatchPreviewEmails extends Command
{
    protected $signature = 'matches:send-preview
                            {--dry-run : Show recipient count without sending anything}
                            {--test= : Send one real email to this address only, as a male recipient (no queue, not tied to any real member)}';

    protected $description = 'Email active members a preview of a few opposite-gender curated profiles';

    private function eligibleUsersQuery()
    {
        return User::where('active', 1)
            ->where('admin', 0)
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
        $this->info("Eligible for match preview: {$total}");

        if ($this->option('dry-run')) {
            return 0;
        }

        if ($total === 0) {
            $this->info('Nobody currently eligible.');
            return 0;
        }

        $dispatched = 0;
        $this->eligibleUsersQuery()->orderBy('users.id')->chunkById(500, function ($users) use (&$dispatched) {
            foreach ($users as $user) {
                SendMatchPreviewEmailJob::dispatch($user->id, $user->email, $user->first_name ?: 'there', $user->gender);
                $dispatched++;
            }
        }, 'users.id', 'id');

        $this->info("Dispatched {$dispatched} match preview emails.");
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
