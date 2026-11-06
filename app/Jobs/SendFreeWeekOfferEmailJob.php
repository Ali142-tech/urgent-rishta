<?php

namespace App\Jobs;

use App\CampaignSend;
use App\Jobs\Concerns\ClassifiesMailFailures;
use App\Mail\ProfileFreeWeekOffer;
use App\Services\ProfileWizard;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends one "complete your profile → 1 week free access" email. Same queue ("campaigns"), rate limiter and retry
 * schedule as SendProfileCompletionEmailJob. The missing-fields list is computed at send time, so it is always current;
 * a member who finished their profile in the meantime is simply dropped (row deleted, nothing sent).
 */
class SendFreeWeekOfferEmailJob implements ShouldQueue
{
    use ClassifiesMailFailures, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 6;
    public $timeout = 60;

    public function __construct(public int $campaignSendId)
    {
        $this->onQueue('campaigns');
    }

    public function middleware(): array
    {
        return [new RateLimited('campaign-mail')];
    }

    public function backoff(): array
    {
        return [120, 600, 1800, 3600, 7200];
    }

    public function handle(ProfileWizard $wizard): void
    {
        $row = CampaignSend::find($this->campaignSendId);
        if (!$row || $row->status === 'sent') {
            return;
        }
        $user = User::where('dataid', $row->dataid)->first();
        if (!$user || empty($user->email)) {
            $row->delete();
            return;
        }
        $status = $wizard->status($user);
        if ($status['complete']) {
            $row->delete();
            return;
        }

        $row->increment('attempts');
        try {
            Mail::to($user->email)->send(new ProfileFreeWeekOffer($user->first_name ?: 'there', $status['percent'], $status['missing']));
            $row->update(['status' => 'sent', 'sent_at' => now(), 'last_error' => null]);
        } catch (Throwable $e) {
            $row->update(['last_error' => $e->getMessage()]);
            if ($this->isPermanentMailFailure($e)) {
                Log::warning("Free-week offer permanently failed for {$row->dataid}: " . $e->getMessage());
                $this->fail($e);
                return;
            }
            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        CampaignSend::where('id', $this->campaignSendId)->update(['status' => 'failed', 'last_error' => $e->getMessage()]);
    }
}
