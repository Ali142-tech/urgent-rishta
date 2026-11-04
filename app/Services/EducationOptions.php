<?php

namespace App\Services;

use App\MasterData;
use Illuminate\Support\Facades\DB;

/**
 * The education dropdown: the client's list (config/education_options.php) in the client's order, taken from the masterdata
 * EDUCATION rows. A member whose saved education is an older / typed one keeps it (it is added to the list for them, or shown
 * as "Other (Please Specify)" with their own words). "Other (Please Specify)" lets a member type their own: that text becomes its
 * own EDUCATION row, so labels, matching and filters keep working on ids as before.
 */
class EducationOptions
{
    /** Every name the dropdown offers, in order, ending with the "other" choice. */
    public static function names(): array
    {
        $names = array_values(array_unique(config('education_options.list', [])));
        $names[] = config('education_options.other', 'Other (Please Specify)');
        return $names;
    }

    /** @return \Illuminate\Support\Collection<int, MasterData> the rows, in the client's order */
    public static function rows()
    {
        $byName = MasterData::where('type', 'EDUCATION')->whereIn('name', self::names())->get()->keyBy(fn ($r) => mb_strtolower($r->name));
        return collect(self::names())->map(fn ($n) => $byName->get(mb_strtolower($n)))->filter()->values();
    }

    /**
     * The list for a dropdown. $currentId: the member's saved education — when it is not in the list (an older one) it is added at
     * the top so opening and saving a form never loses it. $withOther false hides "Other (Please Specify)" (partner preferences).
     */
    public static function list($currentId = null, bool $withOther = true)
    {
        $rows = self::rows();
        if (!$withOther) {
            $rows = $rows->reject(fn ($r) => $r->dataid === self::otherId())->values();
        }
        if ($currentId && !$rows->contains(fn ($r) => (string) $r->dataid === (string) $currentId)) {
            $current = MasterData::where(['type' => 'EDUCATION', 'dataid' => (string) $currentId])->first();
            if ($current) {
                $rows = $rows->prepend($current)->values();
            }
        }
        return $rows;
    }

    /** dataid of the "Other (Please Specify)" row. */
    public static function otherId(): ?string
    {
        static $id = false;
        if ($id === false) {
            $id = MasterData::where('type', 'EDUCATION')->where('name', config('education_options.other', 'Other (Please Specify)'))->value('dataid');
            $id = $id === null ? null : (string) $id;
        }
        return $id;
    }

    /** Is this education id one the dropdown offers (i.e. not an older / typed one)? */
    public static function inList($id): bool
    {
        return self::rows()->contains(fn ($r) => (string) $r->dataid === (string) $id);
    }

    /** What the member typed under "Other", cleaned; null when it is not usable. */
    public static function cleanTyped(?string $text): ?string
    {
        $t = trim(preg_replace('/\s+/u', ' ', (string) $text));
        if ($t === '' || mb_strlen($t) < 2 || mb_strlen($t) > 60 || !preg_match('/^[\p{L}\p{N} .,()&\/+\'’\-]+$/u', $t)) {
            return null;
        }
        return $t;
    }

    /**
     * Turns the choice into the education id to save. When "Other (Please Specify)" was chosen, the typed text becomes (or finds) an
     * EDUCATION row of its own; null when "Other" was chosen without usable text. Any other id is returned as it is.
     */
    public static function resolve($educationId, ?string $typed): ?string
    {
        if ((string) $educationId !== (string) self::otherId()) {
            return $educationId === null || $educationId === '' ? null : (string) $educationId;
        }
        $clean = self::cleanTyped($typed);
        if ($clean === null) {
            return null;
        }
        $existing = MasterData::where('type', 'EDUCATION')->whereRaw('lower(name) = ?', [mb_strtolower($clean)])->value('dataid');
        if ($existing) {
            return (string) $existing;
        }
        $next = (int) DB::table('masterdata')->selectRaw('max(cast(dataid as unsigned)) as m')->value('m') + 1;
        DB::table('masterdata')->insert(['dataid' => (string) $next, 'type' => 'EDUCATION', 'subtype' => '', 'abbreviation' => '', 'name' => $clean, 'created_at' => now(), 'updated_at' => now()]);
        return (string) $next;
    }
}
