<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The client's education list (config/education_options.php) as masterdata EDUCATION rows. A name that already exists (Diploma, MBBS,
 * BDS …) keeps its row, so members who chose it are untouched; the rest are added.
 */
return new class extends Migration
{
    public function up(): void
    {
        $names = array_values(array_unique(config('education_options.list', [])));
        $names[] = config('education_options.other', 'Other (Please Specify)');
        $next = (int) DB::table('masterdata')->selectRaw('max(cast(dataid as unsigned)) as m')->value('m');
        $now = now();

        foreach ($names as $name) {
            $exists = DB::table('masterdata')->where('type', 'EDUCATION')->whereRaw('lower(name) = ?', [mb_strtolower($name)])->exists();
            if (!$exists) {
                DB::table('masterdata')->insert(['dataid' => (string) (++$next), 'type' => 'EDUCATION', 'subtype' => '', 'abbreviation' => '', 'name' => $name, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        // the rows are ordinary masterdata; members may already use them, so nothing is removed
    }
};
