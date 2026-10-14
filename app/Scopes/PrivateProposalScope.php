<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Hides private proposals (proposals.is_private) from everyone except: admins, the team member who added the proposal,
 * and team members an admin allowed (team_members.can_view_private). Site members and visitors never see them.
 * Applied to every Proposal query, so search, AI matches, counts, share links and the member side all follow it.
 * Console commands (cron, migrations, seeders) are not filtered unless $skipInConsole is turned off (used by tests).
 */
class PrivateProposalScope implements Scope
{
    public static bool $skipInConsole = true;

    public function apply(Builder $builder, Model $model): void
    {
        if (self::$skipInConsole && app()->runningInConsole()) {
            return;
        }

        $private = $model->qualifyColumn('is_private');

        $team = Auth::guard('team')->user();
        if ($team) {
            if ($team->is_admin || $team->can_view_private) {
                return;
            }
            $builder->where(fn ($q) => $q->where($private, false)->orWhere($model->qualifyColumn('added_by'), $team->id));
            return;
        }

        $web = Auth::guard('web')->user();
        if ($web && !empty($web->admin)) {
            return;
        }

        $builder->where($private, false);
    }
}
