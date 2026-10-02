<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ONE-TIME CLEAN-UP — delete this file after it has run.
 *
 * Removes what the old design kept in `users`:
 *   1. old team proposals   (users.added_by is set) — with their photos, via proposals:purge-legacy
 *   2. old matchmakers      (users flagged is_team_member / matchmaker_status; admins are never touched)
 *
 * RUN IT BEFORE `php artisan migrate`: the migration drops the matchmaker flag columns, and
 * without them the old matchmakers can't be told apart from other members. Run after the
 * migration it still removes the proposals, and tells you the matchmakers could not be found.
 *
 *   php artisan db:seed --class=RemoveLegacyTeamDataSeeder
 *
 * A JSON backup of everything removed is written to storage/app first.
 */
class RemoveLegacyTeamDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Old team proposals (the command also writes its own backup).
        Artisan::call('proposals:purge-legacy', ['--force' => true]);
        $this->command?->line(trim(Artisan::output()));

        // 2. Old matchmakers.
        if (!Schema::hasColumn('users', 'is_team_member')) {
            $this->command?->warn('The matchmaker flag columns are already gone (migration has run), so the old matchmakers cannot be identified. Skipped.');
            return;
        }

        $matchmakers = DB::table('users')
            ->where('admin', 0)
            ->where(function ($q) {
                $q->where('is_team_member', 1)->orWhereNotNull('matchmaker_status');
            })
            ->get();

        if ($matchmakers->isEmpty()) {
            $this->command?->info('No old matchmakers found.');
            return;
        }

        $ids = $matchmakers->pluck('id')->all();
        $images = DB::table('images')->whereIn('user_id', $ids)->get();

        $backup = storage_path('app/legacy-matchmakers-backup-' . date('Ymd-His') . '.json');
        file_put_contents($backup, json_encode(['users' => $matchmakers, 'images' => $images], JSON_PRETTY_PRINT));

        DB::transaction(function () use ($ids) {
            DB::table('images')->whereIn('user_id', $ids)->delete();
            DB::table('notifications')->where('notifiable_type', 'App\\User')->whereIn('notifiable_id', $ids)->delete();
            DB::table('users')->whereIn('id', $ids)->where('admin', 0)->delete();
        });

        // Their photo files (original + thumbnails).
        foreach ($images as $image) {
            foreach ((array) $image as $value) {
                if (is_string($value) && preg_match('/\.(jpe?g|png|webp|gif)$/i', $value)) {
                    foreach (['', 'thumbnail_', 'thumbnail_sm_', 'thumbnail_md_', 'watermark_'] as $prefix) {
                        @unlink(public_path(dirname($value) . '/' . $prefix . basename($value)));
                    }
                }
            }
        }

        $this->command?->info('Removed ' . count($ids) . ' old matchmaker account(s): ' . $matchmakers->pluck('email')->implode(', '));
        $this->command?->line('Backup: ' . $backup);
    }
}
