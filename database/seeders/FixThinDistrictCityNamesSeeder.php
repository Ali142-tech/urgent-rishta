<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Cleans up a specific data artifact from the original city-import fallback
 * logic (MissingCitiesSeeder): for a state whose name couldn't be matched
 * against the source geographic dataset, the STATE's own full name (e.g.
 * "Bandarban District") was used verbatim as its one fallback CITY name too
 * — which works (never blocks registration) but reads oddly, since real
 * places aren't named "Bandarban District", just "Bandarban".
 *
 * This only touches states where the city name is an EXACT copy of the
 * state's own name — i.e. the fallback actually fired — and only strips the
 * generic administrative-unit words (District/Division/Zila/Hill District)
 * to recover the real place name. This is safe specifically for Bangladesh
 * (and similarly-structured countries): each district there is
 * conventionally named after its own headquarters town, so "X District" ->
 * "X" is always the correct real city name, not a guess.
 *
 * Does NOT touch states whose single city is already a genuinely different
 * real name (e.g. Noakhali District -> Maijdi, Shariatpur District ->
 * Palang) — those are already correct, just legitimately small districts.
 *
 * Idempotent: only renames rows that still match the exact-copy pattern, so
 * running it again after it's already cleaned something up is a no-op for
 * that row.
 *
 * Run with:
 *   php artisan db:seed --class=Database\\Seeders\\FixThinDistrictCityNamesSeeder
 */
class FixThinDistrictCityNamesSeeder extends Seeder
{
    private const SUFFIXES = [' Hill District', ' District', ' Division', ' Zila'];

    public function run(): void
    {
        $states = DB::table('masterdata')->where('type', 'STATE')->get(['dataid', 'name']);
        $renamed = 0;

        foreach ($states as $state) {
            $cities = DB::table('masterdata')->where('type', 'CITY')->where('subtype', $state->dataid)->get(['id', 'name']);
            if ($cities->count() !== 1) {
                continue; // only touch genuinely single-city (thin/fallback) states
            }

            $city = $cities->first();
            if ($city->name !== $state->name) {
                continue; // already a distinct real name, leave it alone
            }

            $stripped = $city->name;
            foreach (self::SUFFIXES as $suffix) {
                if (str_ends_with($stripped, $suffix)) {
                    $stripped = substr($stripped, 0, -strlen($suffix));
                    break;
                }
            }

            if ($stripped === $city->name || trim($stripped) === '') {
                continue; // no administrative suffix to strip, nothing to fix
            }

            DB::table('masterdata')->where('id', $city->id)->update([
                'name' => trim($stripped),
                'updated_at' => now(),
            ]);
            $this->command->info("Renamed city for \"{$state->name}\": \"{$city->name}\" -> \"" . trim($stripped) . "\"");
            $renamed++;
        }

        $this->command->info("Done: renamed {$renamed} fallback city entries to their real place name.");
    }
}
