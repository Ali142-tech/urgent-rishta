<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Education is now picked from the client's list (config/proposal_options.php) or typed in, so it is
 * stored as text instead of a masterdata id. Existing ids are converted to their names.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('education', 150)->nullable()->change();
        });

        $names = DB::table('masterdata')->where('type', 'EDUCATION')->pluck('name', 'dataid');

        foreach (DB::table('proposals')->whereNotNull('education')->get(['id', 'education', 'pref_educations']) as $row) {
            $update = [];
            if (isset($names[$row->education])) {
                $update['education'] = $names[$row->education];
            }
            $prefs = json_decode($row->pref_educations ?? 'null', true);
            if (is_array($prefs)) {
                $update['pref_educations'] = json_encode(array_map(fn ($v) => $names[$v] ?? $v, $prefs));
            }
            if ($update) {
                DB::table('proposals')->where('id', $row->id)->update($update);
            }
        }
        // Proposals that only have partner education choices.
        foreach (DB::table('proposals')->whereNull('education')->whereNotNull('pref_educations')->get(['id', 'pref_educations']) as $row) {
            $prefs = json_decode($row->pref_educations, true);
            if (is_array($prefs)) {
                DB::table('proposals')->where('id', $row->id)->update(['pref_educations' => json_encode(array_map(fn ($v) => $names[$v] ?? $v, $prefs))]);
            }
        }
    }

    public function down(): void
    {
        // Names stay as text; the column length is left wide.
    }
};
