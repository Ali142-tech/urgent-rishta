<?php

namespace App\Services;

use App\CampaignSend;
use App\Jobs\SendProfileCompletionEmailJob;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Shared by both the CLI command (php artisan campaign:send-profile-completion)
 * and the admin "Profile Completion Campaign" page, so the eligibility rule,
 * dispatch logic, and status reporting can't drift between the two entry
 * points.
 *
 * Eligibility: every active, non-admin member with an email — a blanket
 * resend matching the existing gender campaign's approach, rather than
 * narrowing to a specific "incomplete" signal (an earlier attempt at
 * targeting only photo_verification_status IN ('pending','resubmit') only
 * matched 106 of ~18,000 members, since a prior migration backfilled nearly
 * everyone to 'verified' when that feature launched — too narrow for the
 * intended reach).
 */
class ProfileCompletionCampaignService
{
    public const CAMPAIGN_KEY = 'profile_completion';

    public function eligibleUsersQuery()
    {
        return User::where('active', 1)
            ->where('admin', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('campaign_sends')
                    ->whereColumn('campaign_sends.dataid', 'users.dataid')
                    ->where('campaign_sends.campaign_key', self::CAMPAIGN_KEY);
            });
    }

    /**
     * Dispatches jobs for up to $limit eligible users (null = everyone
     * currently eligible). Chunked so 18,000+ eligible rows are never loaded
     * into memory at once. Returns how many were dispatched.
     */
    public function dispatchEligible(?int $limit = null): int
    {
        $dispatched = 0;
        $remaining = $limit;

        $this->eligibleUsersQuery()->orderBy('users.id')->chunkById(500, function ($users) use (&$dispatched, &$remaining) {
            foreach ($users as $user) {
                if ($remaining !== null && $remaining <= 0) {
                    return false;
                }

                $row = CampaignSend::create([
                    'dataid' => $user->dataid,
                    // campaign_sends.gender is NOT NULL (inherited from the
                    // gender-only campaign this table originally tracked) —
                    // this campaign targets all active members regardless of
                    // gender, and some real accounts have a null gender, so
                    // fall back to a placeholder rather than violate the column.
                    'gender' => $user->gender ?? 'unknown',
                    'campaign_key' => self::CAMPAIGN_KEY,
                    'email' => $user->email,
                    'status' => 'queued',
                ]);

                SendProfileCompletionEmailJob::dispatch(
                    $row->id,
                    $user->email,
                    $user->first_name ?: 'there'
                );

                $dispatched++;
                if ($remaining !== null) {
                    $remaining--;
                }
            }
        }, 'users.id', 'id');

        return $dispatched;
    }

    /** Re-queues rows currently marked failed. Returns how many were re-queued. */
    public function retryFailed(): int
    {
        $failedRows = CampaignSend::where('campaign_key', self::CAMPAIGN_KEY)->where('status', 'failed')->get();
        if ($failedRows->isEmpty()) {
            return 0;
        }

        $usersByDataid = User::whereIn('dataid', $failedRows->pluck('dataid'))->get()->keyBy('dataid');

        $requeued = 0;
        foreach ($failedRows as $row) {
            $user = $usersByDataid->get($row->dataid);
            if (!$user || empty($user->email)) {
                continue;
            }

            $row->update(['status' => 'queued', 'attempts' => 0, 'last_error' => null]);

            SendProfileCompletionEmailJob::dispatch($row->id, $user->email, $user->first_name ?: 'there');
            $requeued++;
        }

        return $requeued;
    }

    /** @return array{total_audience:int,sent:int,failed:int,queued:int,not_yet_dispatched:int,paused:bool} */
    public function status(): array
    {
        $totalAudience = User::where('active', 1)
            ->where('admin', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();

        $sent = CampaignSend::where('campaign_key', self::CAMPAIGN_KEY)->where('status', 'sent')->count();
        $failed = CampaignSend::where('campaign_key', self::CAMPAIGN_KEY)->where('status', 'failed')->count();
        $queued = CampaignSend::where('campaign_key', self::CAMPAIGN_KEY)->where('status', 'queued')->count();

        return [
            'total_audience' => $totalAudience,
            'sent' => $sent,
            'failed' => $failed,
            'queued' => $queued,
            'not_yet_dispatched' => max(0, $totalAudience - $sent - $failed - $queued),
            'paused' => $this->isPaused(),
        ];
    }

    public function isPaused(): bool
    {
        return (bool) Cache::get(SendProfileCompletionEmailJob::CACHE_KEY_PAUSED);
    }

    /** Pausing stops already-queued jobs from actually sending (they self-release every 5 min) without discarding them or stopping the worker process. */
    public function pause(): void
    {
        Cache::forever(SendProfileCompletionEmailJob::CACHE_KEY_PAUSED, true);
    }

    public function resume(): void
    {
        Cache::forget(SendProfileCompletionEmailJob::CACHE_KEY_PAUSED);
    }
}
