<?php

namespace App\Services;

use App\CampaignSend;
use App\Jobs\SendFreeWeekOfferEmailJob;
use App\User;
use Illuminate\Support\Facades\DB;

/**
 * One-time "complete your profile to 100% and get 1 week free access" campaign.
 *
 *  - Audience: active, non-admin members with an email whose profile is NOT 100% complete (ProfileWizard::status),
 *    and who have not been sent this campaign before (campaign_sends row, key below).
 *  - Email only: it is an incentive message; the system does not grant any access itself.
 */
class FreeWeekCampaignService
{
    public const CAMPAIGN_KEY = 'profile_free_week';

    public function __construct(private ProfileWizard $wizard)
    {
    }

    /** Members who have no row for this campaign yet (the "not complete" test is applied per user in PHP). */
    public function candidatesQuery()
    {
        return User::where('active', 1)
            ->where('admin', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('campaign_sends')
                    ->whereColumn('campaign_sends.dataid', 'users.dataid')
                    ->where('campaign_sends.campaign_key', self::CAMPAIGN_KEY);
            });
    }

    /**
     * Calls $each(User $user, array $status) for every candidate whose profile is incomplete, until $each returns false.
     * Chunked, so ~18k users are never in memory at once.
     */
    public function eachIncomplete(callable $each): void
    {
        $this->candidatesQuery()->orderBy('users.id')->chunkById(300, function ($users) use ($each) {
            foreach ($users as $user) {
                $status = $this->wizard->status($user);
                if ($status['complete']) {
                    continue;
                }
                if ($each($user, $status) === false) {
                    return false;
                }
            }
        }, 'users.id', 'id');
    }

    public function countIncomplete(): int
    {
        $n = 0;
        $this->eachIncomplete(function () use (&$n) {
            $n++;
        });
        return $n;
    }

    public function dispatchEligible(?int $limit = null): int
    {
        $dispatched = 0;
        $this->eachIncomplete(function (User $user) use (&$dispatched, $limit) {
            if ($limit !== null && $dispatched >= $limit) {
                return false;
            }
            $row = CampaignSend::create([
                'dataid' => $user->dataid,
                'gender' => $user->gender ?? 'unknown',
                'campaign_key' => self::CAMPAIGN_KEY,
                'email' => $user->email,
                'status' => 'queued',
            ]);
            SendFreeWeekOfferEmailJob::dispatch($row->id);
            $dispatched++;
        });
        return $dispatched;
    }

    /** @return array{sent:int,queued:int,failed:int} */
    public function status(): array
    {
        $rows = CampaignSend::where('campaign_key', self::CAMPAIGN_KEY);
        return [
            'sent' => (clone $rows)->where('status', 'sent')->count(),
            'queued' => (clone $rows)->where('status', 'queued')->count(),
            'failed' => (clone $rows)->where('status', 'failed')->count(),
        ];
    }
}
