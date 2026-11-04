<?php

namespace App\Services;

/**
 * The profession dropdown for members: the client's list first (config/profession_options.php), then the professions that were
 * already on offer (config/proposal_options.php) that the client's list does not repeat. "Other (Please Specify)" is the last
 * choice; what a member types there is saved as their profession text.
 */
class ProfessionOptions
{
    public const OTHER_VALUE = '__other';

    public static function otherLabel(): string
    {
        return config('profession_options.other', 'Other (Please Specify)');
    }

    /** Every profession name offered, without repeats (compared ignoring case and spacing). */
    public static function list(): array
    {
        $seen = [];
        $out = [];
        foreach (array_merge(config('profession_options.client', []), config('proposal_options.profession', [])) as $name) {
            $key = mb_strtolower(preg_replace('/\s+/u', ' ', trim($name)));
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $name;
        }
        return $out;
    }

    public static function inList(?string $name): bool
    {
        return $name !== null && in_array(mb_strtolower(trim($name)), array_map('mb_strtolower', self::list()), true);
    }

    /** What the member typed under "Other", cleaned; null when it is not usable. */
    public static function cleanTyped(?string $text): ?string
    {
        $t = trim(preg_replace('/\s+/u', ' ', (string) $text));
        if ($t === '' || mb_strlen($t) < 2 || mb_strlen($t) > 100 || !preg_match('/^[\p{L}\p{N} .,()&\/+\'’\-]+$/u', $t)) {
            return null;
        }
        return $t;
    }
}
