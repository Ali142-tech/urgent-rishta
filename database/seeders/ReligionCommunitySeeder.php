<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\MasterData;

/**
 * Fixes the registration form's "Community" dropdown always showing
 * Muslim-only caste/tribe names regardless of which religion was selected
 * (client report, Sep 2026) — there was no religion->community link
 * anywhere in the data at all; every one of the ~363 existing CASTE rows
 * was a Pakistani-Muslim caste/tribe name with no way to tell them apart
 * from a genuinely universal option.
 *
 * Does two things, both idempotent (safe to re-run any time, e.g. after
 * deploying to a fresh environment):
 *
 *   1. Tags every existing CASTE row with subtype = Muslim's religion
 *      dataid (739) — except "Other" (dataid 369), which is deliberately
 *      left with an empty subtype so it acts as a universal fallback and
 *      always shows up for every religion (see Controller::castes(), which
 *      matches on subtype = the selected religion OR subtype is empty).
 *      Only touches rows that don't already have a subtype set, so this
 *      never clobbers a caste that's already been correctly tagged.
 *
 *   2. Adds a starting set of real community/denomination names for the
 *      other religions (Hindu, Christian, Sikh, Parsi, Jain, Buddhist,
 *      Jewish) from database/seeders/data/religion_communities.json,
 *      skipping any (religion, name) pair that already exists so this
 *      never creates duplicates. "No Religion"/"Other"/"Spiritual" are
 *      deliberately not given dedicated entries — the "Other" fallback
 *      above already covers them.
 *
 * Compiled from general knowledge of common South Asian/Pakistani
 * community and denominational naming conventions — NOT sourced from any
 * official list or the client's own data, so treat it as a reasonable
 * starting point to correct/extend, not a verified authority. Edit
 * database/seeders/data/religion_communities.json and re-run to add more.
 *
 * Run with:
 *   php artisan db:seed --class=Database\\Seeders\\ReligionCommunitySeeder
 */
class ReligionCommunitySeeder extends Seeder
{
    /** Muslim's masterdata dataid — see MasterData::where('type','RELIGION'). */
    const MUSLIM_RELIGION_ID = '739';

    /** "Other" — the universal fallback caste entry, deliberately left with no subtype. */
    const OTHER_CASTE_DATAID = '369';

    public function run(): void
    {
        $tagged = MasterData::where('type', 'CASTE')
            ->where('dataid', '!=', self::OTHER_CASTE_DATAID)
            ->where(function ($q) {
                $q->whereNull('subtype')->orWhere('subtype', '');
            })
            ->update(['subtype' => self::MUSLIM_RELIGION_ID]);
        $this->command->info("Tagged {$tagged} untagged caste rows as Muslim.");

        $path = __DIR__ . '/data/religion_communities.json';
        if (!file_exists($path)) {
            $this->command->error("Data file not found: {$path}");
            return;
        }
        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            $this->command->error('Could not parse religion_communities.json');
            return;
        }

        $inserted = 0;
        $skipped = 0;

        foreach ($data as $religionId => $entry) {
            $prefix = $entry['prefix'];
            foreach ($entry['names'] as $i => $name) {
                $alreadyExists = MasterData::where('type', 'CASTE')
                    ->where('subtype', $religionId)
                    ->where('name', $name)
                    ->exists();
                if ($alreadyExists) {
                    $skipped++;
                    continue;
                }

                // Find a free dataid under this prefix rather than assuming
                // position-in-list stays stable across edits to the JSON.
                $n = 1;
                do {
                    $dataid = $prefix . str_pad($n, 3, '0', STR_PAD_LEFT);
                    $taken = MasterData::where('type', 'CASTE')->where('dataid', $dataid)->exists();
                    $n++;
                } while ($taken);

                // Deliberately leave `order` untouched (stays NULL, matching
                // every pre-existing caste row) — MasterData::$fillable
                // doesn't even include it, so passing a real number here
                // would silently be dropped for a plain create() call, but
                // NOT for a raw value that slips through some other way;
                // an earlier version of this seeder did exactly that (see
                // git history) and one real numeric `order` among a sea of
                // NULLs jumped that single row to the front of the list in
                // castes()'s `ORDER BY order DESC` (MySQL sorts NULL last
                // in DESC), ahead of every alphabetically-ordered entry.
                MasterData::create([
                    'dataid' => $dataid,
                    'type' => 'CASTE',
                    'subtype' => $religionId,
                    'name' => $name,
                ]);
                $inserted++;
            }
        }

        $this->command->info("Inserted {$inserted} new community entries, skipped {$skipped} already present.");
    }
}
