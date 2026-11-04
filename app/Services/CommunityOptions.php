<?php

namespace App\Services;

use App\MasterData;
use Illuminate\Support\Facades\DB;

/**
 * The community (caste) choices for a member: they follow the country they live in, then their religion.
 *
 * A country with its own list (rows linked to it in `community_countries`) shows that list, narrowed to the chosen religion
 * (rows made for every religion — subtype '0', e.g. Nepal's or Afghanistan's — always count):
 *   - "exclusive" countries (India, Nepal, Afghanistan, Iran …) show only their own list;
 *   - the others (Malaysia, Kenya, South Africa, Mauritius …, where many South Asians live) show their own groups first and the
 *     common list for the religion after them.
 * A country without a list, or a religion the country's list has nothing for, shows the common list for the religion, exactly as
 * the site always has. "Other" (no religion) is always the last choice.
 */
class CommunityOptions
{
    /** @return \Illuminate\Support\Collection<int, MasterData> */
    public static function for($country, $religion)
    {
        $links = $country ? DB::table('community_countries')->where('country_dataid', (string) $country)->get(['caste_dataid', 'exclusive']) : collect();

        if ($links->isNotEmpty() && $religion) {
            $own = MasterData::where('type', 'CASTE')->whereIn('dataid', $links->pluck('caste_dataid'))
                ->where(fn ($q) => $q->where('subtype', (string) $religion)->orWhere('subtype', '0'))->get();

            if ($own->isNotEmpty()) {
                $own = $own->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
                $exclusive = $links->every(fn ($l) => (bool) ($l->exclusive ?? true));

                if ($exclusive) {
                    $other = MasterData::where('type', 'CASTE')->where(fn ($q) => $q->whereNull('subtype')->orWhere('subtype', ''))->get();
                    return $own->concat($other);
                }

                // own groups first, then the common list for the religion (without repeating a name)
                $common = self::common($religion);
                $names = $own->pluck('name')->map(fn ($n) => mb_strtolower($n))->all();
                return $own->concat($common->reject(fn ($c) => in_array(mb_strtolower($c->name), $names, true)))->values();
            }
        }

        return self::common($religion);
    }

    /** The common list for a religion (plus the "Other" entries that belong to every religion). */
    private static function common($religion)
    {
        return MasterData::where('type', 'CASTE')
            ->where(fn ($q) => $q->where('subtype', $religion)->orWhereNull('subtype')->orWhere('subtype', ''))
            ->orderByRaw("name = 'Other' ASC")->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get();
    }
}
