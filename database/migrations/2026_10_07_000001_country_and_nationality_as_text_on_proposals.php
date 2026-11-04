<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Country of residence and nationality are now picked from the client's lists (config/proposal_options.php) or typed in,
 * so they are stored as text instead of a masterdata id. Existing ids become names: the country by its list name, the
 * nationality by the matching nationality (Pakistan -> Pakistani). A country that is not in the lists keeps its masterdata name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('con_of_residence', 100)->nullable()->change();
            $table->string('con_of_citizenship', 100)->nullable()->change();
        });

        $listCountries = array_keys(config('proposal_options.countries'));
        $listNationalities = config('proposal_options.nationalities');
        $names = DB::table('masterdata')->where('type', 'COUNTRY')->pluck('name', 'dataid');

        // "United Kingdom" (masterdata) -> "United Kingdom (UK)" (list): match on the name before any " (".
        $toListCountry = function (string $name) use ($listCountries) {
            foreach ($listCountries as $i => $listName) {
                if (strcasecmp(trim(preg_replace('/\s*\(.*\)$/', '', $listName)), $name) === 0) {
                    return [$listName, $i];
                }
            }
            return [$name, null];
        };

        foreach (DB::table('proposals')->get(['id', 'con_of_residence', 'con_of_citizenship', 'pref_nationalities']) as $row) {
            $update = [];

            if ($row->con_of_residence !== null && isset($names[$row->con_of_residence])) {
                $update['con_of_residence'] = $toListCountry($names[$row->con_of_residence])[0];
            }
            if ($row->con_of_citizenship !== null && isset($names[$row->con_of_citizenship])) {
                [$countryName, $i] = $toListCountry($names[$row->con_of_citizenship]);
                $update['con_of_citizenship'] = $i !== null ? $listNationalities[$i] : $countryName;
            }
            $prefs = json_decode($row->pref_nationalities ?? 'null', true);
            if (is_array($prefs)) {
                $update['pref_nationalities'] = json_encode(array_map(function ($v) use ($names, $toListCountry, $listNationalities) {
                    if ($v === '__any' || !isset($names[$v])) {
                        return $v;
                    }
                    [$countryName, $i] = $toListCountry($names[$v]);
                    return $i !== null ? $listNationalities[$i] : $countryName;
                }, $prefs));
            }

            if ($update) {
                DB::table('proposals')->where('id', $row->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        // Names stay as text; the columns are left wide.
    }
};