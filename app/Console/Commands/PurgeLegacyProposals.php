<?php

namespace App\Console\Commands;

use App\Images;
use App\PartnerPreference;
use App\Proposal;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * One-off cleanup after proposals moved to their own `proposals` table: removes
 * the OLD proposals that still sit in `users` as login-less shell rows
 * (users.added_by IS NOT NULL) together with their photos (rows + files in
 * public/users), partner-requirement rows and now-dangling notifications.
 *
 *   php artisan proposals:purge-legacy --dry-run     (report only)
 *   php artisan proposals:purge-legacy               (asks to confirm)
 *   php artisan proposals:purge-legacy --force       (no question)
 *
 * A JSON backup of everything it deletes is written to storage/app first.
 * The old proposals are NOT copied into the new table (the client agreed they
 * can be dropped).
 */
class PurgeLegacyProposals extends Command
{
    protected $signature = 'proposals:purge-legacy {--dry-run : Only report what would be deleted} {--force : Do not ask for confirmation}';

    protected $description = 'Delete the old proposal rows that still live in the users table (backs them up first)';

    public function handle(): int
    {
        $users = User::withTrashed()->whereNotNull('added_by')->get();
        if ($users->isEmpty()) {
            $this->info('No legacy proposals found in the users table. Nothing to do.');
            return self::SUCCESS;
        }

        $ids = $users->pluck('id');
        $images = Images::whereIn('user_id', $ids)->get();
        $prefs = PartnerPreference::whereIn('user_id', $ids)->get();
        $notifications = $this->staleNotifications();

        $this->line('Legacy proposals in users : ' . $users->count());
        $this->line('Their photos              : ' . $images->count());
        $this->line('Their partner requirements: ' . $prefs->count());
        $this->line('Dangling notifications    : ' . $notifications->count());

        if ($this->option('dry-run')) {
            $this->warn('Dry run — nothing was deleted.');
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Delete all of the above? A backup file is written first.')) {
            $this->warn('Cancelled.');
            return self::FAILURE;
        }

        $backup = storage_path('app/legacy-proposals-backup-' . date('Ymd-His') . '.json');
        File::ensureDirectoryExists(dirname($backup));
        File::put($backup, json_encode([
            'users' => $users->toArray(),
            'images' => $images->toArray(),
            'partner_preferences' => $prefs->toArray(),
            'notifications' => $notifications->toArray(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info('Backup written: ' . $backup);

        DB::transaction(function () use ($images, $prefs, $users, $notifications) {
            Images::whereIn('id', $images->pluck('id'))->delete();
            PartnerPreference::whereIn('id', $prefs->pluck('id'))->delete();
            DB::table('notifications')->whereIn('id', $notifications->pluck('id'))->delete();
            User::withTrashed()->whereIn('id', $users->pluck('id'))->forceDelete();
        });

        // Files go after the database commit succeeded.
        $removed = 0;
        $publicPath = public_path('users');
        foreach ($images as $image) {
            foreach ($this->derivativePaths($publicPath, $image->name, $image->salt) as $path) {
                if (File::exists($path)) {
                    File::delete($path);
                    $removed++;
                }
            }
        }

        $this->info("Deleted {$users->count()} legacy proposals, {$images->count()} photo rows ({$removed} files), {$prefs->count()} requirement rows, {$notifications->count()} notifications.");
        return self::SUCCESS;
    }

    /** Every file the old upload pipeline wrote for one photo. */
    private function derivativePaths(string $dir, string $name, $salt): array
    {
        $paths = [
            $dir . '/' . $name,
            $dir . '/thumbnail_' . $name,
            $dir . '/thumbnail_md_' . $name,
            $dir . '/thumbnail_sm_' . $name,
            $dir . '/watermark_' . $name,
        ];
        $parts = explode('_', $name);
        if ($salt !== null && count($parts) >= 2) {
            $paths[] = $dir . '/' . $parts[0] . $salt . $parts[1];
        }
        return $paths;
    }

    /**
     * "New Proposal" / "AI Match Found" notifications that point at a proposal ID which is not
     * in the new table (i.e. one of the old proposals) — they would only lead to 404s.
     */
    private function staleNotifications()
    {
        $current = Proposal::pluck('reference')->flip();
        return DB::table('notifications')
            ->whereIn('type', ['App\\Notifications\\NewProposalAdded', 'App\\Notifications\\AiMatchFound'])
            ->get(['id', 'type', 'data'])
            ->filter(function ($n) use ($current) {
                $data = json_decode($n->data, true) ?: [];
                return !isset($data['proposalid']) || !$current->has($data['proposalid']);
            })
            ->values();
    }
}
