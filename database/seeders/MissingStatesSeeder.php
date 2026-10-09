<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fills in a fallback STATE (and a fallback CITY under it) for every
 * COUNTRY-type masterdata row that currently has zero states at all (53 of
 * 248 countries as of writing) — this is why the registration form's "Now
 * let's build your Profile" step could hit a dead end ("No states found for
 * your selected country. Go back and pick another country, or contact
 * support.") for anyone who picked one of those countries, with no way
 * forward except abandoning that country entirely.
 *
 * Almost all of the affected countries are small territories/city-states
 * that genuinely have no state/province-level subdivision to begin with
 * (Monaco, Vatican City, Gibraltar, Hong Kong S.A.R., Macau S.A.R., and a
 * long tail of small islands/dependencies) — so rather than inventing fake
 * administrative divisions for them, this uses the SAME fallback approach
 * MissingCitiesSeeder already established for the equivalent city-level
 * gap: the country's own name becomes a single state entry, and (since
 * these are brand-new states MissingCitiesSeeder never had a chance to
 * cover) the country's own name again becomes that state's single city
 * entry, so the full country -> state -> city chain always has at least
 * one option no matter how small/unusual the country.
 *
 * Idempotent / safe to re-run: only touches countries that still have zero
 * states at run time, and only adds a city if that state still has zero
 * cities — so running this again after some of these countries pick up
 * real data another way never creates duplicates.
 *
 * Run with:
 *   php artisan db:seed --class=Database\\Seeders\\MissingStatesSeeder
 */
class MissingStatesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = DB::table('masterdata')->where('type', 'COUNTRY')->get(['dataid', 'name']);

        $stateCounts = DB::table('masterdata')->where('type', 'STATE')
            ->selectRaw('subtype, count(*) as cnt')->groupBy('subtype')->pluck('cnt', 'subtype');

        $now = now();
        $statesInserted = 0;
        $citiesInserted = 0;

        foreach ($countries as $country) {
            if (($stateCounts[$country->dataid] ?? 0) > 0) {
                continue; // already has real state data, leave it alone
            }

            $stateDataid = strtoupper(substr(base_convert(sha1(uniqid((string) mt_rand(), true)), 16, 36), 0, 9));
            DB::table('masterdata')->insert([
                'dataid' => $stateDataid,
                'type' => 'STATE',
                'subtype' => $country->dataid,
                'name' => $country->name,
                'description' => null,
                'order' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $statesInserted++;

            // Brand-new state, so it has zero cities by definition — give it
            // the same one-city fallback immediately rather than waiting on
            // a separate MissingCitiesSeeder run to notice it.
            $cityDataid = strtoupper(substr(base_convert(sha1(uniqid((string) mt_rand(), true)), 16, 36), 0, 9));
            DB::table('masterdata')->insert([
                'dataid' => $cityDataid,
                'type' => 'CITY',
                'subtype' => $stateDataid,
                'name' => $country->name,
                'description' => null,
                'order' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $citiesInserted++;
        }

        $this->command->info("Inserted {$statesInserted} fallback states and {$citiesInserted} fallback cities.");
    }
}
