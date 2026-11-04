<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fills in real city data for every STATE-type masterdata row that currently
 * has zero cities (1,477 of them worldwide as of writing — this is why the
 * registration form's City dropdown could come back completely empty and
 * permanently block signup for anyone in one of those regions).
 *
 * Data source: database/seeders/data/missing_cities.json — built from the
 * public GeoNames dataset (cities5000 + admin1/admin2 boundaries), matched
 * against each state by name. Where a real, verified match was found, real
 * town/city names for that exact region are used (e.g. South Ayrshire, UK ->
 * Ayr, Troon, Prestwick, Girvan). Where no safe automatic match could be made
 * (naming differs too much from GeoNames' scheme to match without risking a
 * WRONG region's cities being attached), the state's own name is used as a
 * single fallback city entry instead — so no state is ever left with zero
 * options, even though it isn't full precise data for that handful of cases.
 *
 * Idempotent / safe to re-run: skips any (state, city name) pair that
 * already exists, so running this twice (or after some of these states
 * already picked up cities another way) never creates duplicates.
 *
 * Run with:
 *   php artisan db:seed --class=Database\\Seeders\\MissingCitiesSeeder
 */
class MissingCitiesSeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__ . '/data/missing_cities.json';
        if (!file_exists($path)) {
            $this->command->error("Data file not found: {$path}");
            return;
        }

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            $this->command->error('Could not parse missing_cities.json');
            return;
        }

        // One query for every (subtype, lowercased name) pair already in the
        // CITY table, so we can skip duplicates without a query per row.
        $existing = DB::table('masterdata')
            ->where('type', 'CITY')
            ->select('subtype', 'name')
            ->get()
            ->map(fn ($row) => $row->subtype . '|' . mb_strtolower($row->name))
            ->flip();

        $now = now();
        $rows = [];
        $skipped = 0;

        foreach ($data as $stateDataid => $cityNames) {
            foreach ($cityNames as $cityName) {
                $key = $stateDataid . '|' . mb_strtolower($cityName);
                if (isset($existing[$key])) {
                    $skipped++;
                    continue;
                }

                $rows[] = [
                    'dataid' => strtoupper(substr(base_convert(sha1(uniqid((string) mt_rand(), true)), 16, 36), 0, 9)),
                    'type' => 'CITY',
                    'subtype' => $stateDataid,
                    'name' => $cityName,
                    'description' => null,
                    'order' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                // Mark as seen so a duplicate name later in the same JSON
                // batch (shouldn't happen, but safe) doesn't insert twice.
                $existing[$key] = true;
            }
        }

        $inserted = 0;
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('masterdata')->insert($chunk);
            $inserted += count($chunk);
        }

        $this->command->info("Inserted {$inserted} new city rows across " . count($data) . " states.");
        if ($skipped > 0) {
            $this->command->info("Skipped {$skipped} that already existed.");
        }
    }
}
