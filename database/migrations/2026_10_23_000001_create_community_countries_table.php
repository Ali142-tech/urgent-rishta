<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Links communities (masterdata CASTE rows) to the countries they belong to, so the signup form can offer the communities of
 * the country a member lives in. India's list is added for Hindu members (existing Hindu communities are linked too); Nepal,
 * Bangladesh and Sri Lanka get lists that apply whatever the religion (subtype '0' — hidden from the religion-based lists).
 * More countries are just more rows here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_countries', function (Blueprint $table) {
            $table->id();
            $table->string('caste_dataid', 20);
            $table->string('country_dataid', 20);
            $table->unique(['caste_dataid', 'country_dataid']);
            $table->index('country_dataid');
        });

        $next = (int) DB::table('masterdata')->selectRaw('max(cast(dataid as unsigned)) as m')->value('m');
        $countryId = fn (string $name) => DB::table('masterdata')->where('type', 'COUNTRY')->where('name', $name)->value('dataid');
        $now = now();

        $add = function (string $country, string $subtype, array $names, bool $linkExistingSubtype = false) use (&$next, $countryId, $now) {
            $cid = $countryId($country);
            if (!$cid) {
                return;
            }
            if ($linkExistingSubtype) {
                foreach (DB::table('masterdata')->where('type', 'CASTE')->where('subtype', $subtype)->pluck('dataid') as $existing) {
                    DB::table('community_countries')->insertOrIgnore(['caste_dataid' => (string) $existing, 'country_dataid' => (string) $cid]);
                }
            }
            foreach ($names as $name) {
                $row = DB::table('masterdata')->where('type', 'CASTE')->where('subtype', $subtype)->whereRaw('lower(name) = ?', [mb_strtolower($name)])->first();
                if ($row) {
                    $dataid = $row->dataid;
                } else {
                    $dataid = (string) (++$next);
                    DB::table('masterdata')->insert(['dataid' => $dataid, 'type' => 'CASTE', 'subtype' => $subtype, 'abbreviation' => '', 'name' => $name, 'created_at' => $now, 'updated_at' => $now]);
                }
                DB::table('community_countries')->insertOrIgnore(['caste_dataid' => (string) $dataid, 'country_dataid' => (string) $cid]);
            }
        };

        // India — Hindu communities (religion "Hindu" = 740)
        $add('India', '740', [
            'Agarwal', 'Ahir / Yadav', 'Arora', 'Bania', 'Brahmin', 'Bunt', 'Chamar / Ravidasia', 'Chettiar', 'Devanga', 'Dhobi',
            'Gounder', 'Gupta', 'Jat', 'Jatav', 'Kayastha', 'Khatri', 'Koli', 'Konkani', 'Kurmi', 'Lingayat', 'Lohana', 'Maheshwari',
            'Maratha', 'Meena', 'Nair', 'Nadar', 'Nai', 'Namasudra', 'OBC / Other Backward Class', 'Patel / Patidar', 'Rajput', 'Reddy',
            'Saini', 'Scheduled Caste (self-described)', 'Scheduled Tribe (self-described)', 'Sharma', 'Shetty', 'Sindhi', 'Teli',
            'Vanniyar', 'Vishwakarma',
        ], true);

        // Whatever the religion (subtype '0': not part of any religion's own list)
        $add('Nepal', '0', ['Bahun / Brahmin', 'Chhetri', 'Dalit (self-described)', 'Gurung', 'Kirati / Rai', 'Limbu', 'Madhesi', 'Magar', 'Newar', 'Thakuri', 'Tamang']);
        $add('Bangladesh', '0', ['Bengali', 'Bihari', 'Chakma', 'Garo', 'Hajong', 'Marma', 'Mro', 'Oraon', 'Santal', 'Tripura']);
        $add('Sri Lanka', '0', ['Govigama', 'Karava', 'Salagama', 'Durava', 'Vellalar', 'Karaiyar', 'Mukkuvar']);
    }

    public function down(): void
    {
        DB::table('masterdata')->where('type', 'CASTE')->where('subtype', '0')->delete();
        Schema::dropIfExists('community_countries');
    }
};
