<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Communities for more countries (the main ethnic / tribal communities a person from there would name — based on the CIA World
 * Factbook "Ethnic groups" entries, tidied by hand).
 *
 *  - Home-country lists (Afghanistan, Iran, Turkey, Indonesia …) replace the common South Asian list for members living there
 *    ("exclusive").
 *  - Countries with a large South Asian community (Malaysia, Kenya, South Africa, Mauritius, Fiji, Trinidad …) show their own
 *    groups first and the common list after them (exclusive = 0), so South Asian members there still find their community.
 *  - Gulf states, the UK, the USA, Canada, Australia and Europe have no list of their own: they keep the common list.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_countries', function (Blueprint $table) {
            $table->boolean('exclusive')->default(true)->after('country_dataid');
        });

        $next = (int) DB::table('masterdata')->selectRaw('max(cast(dataid as unsigned)) as m')->value('m');
        $now = now();
        $aliases = ['Burma' => 'Myanmar', 'Fiji' => 'Fiji Islands'];

        $add = function (string $country, array $names, bool $exclusive = true) use (&$next, $now, $aliases) {
            $lookup = $aliases[$country] ?? $country;
            $cid = DB::table('masterdata')->where('type', 'COUNTRY')->whereRaw('lower(name) = ?', [mb_strtolower($lookup)])->value('dataid');
            if (!$cid) {
                return;
            }
            foreach ($names as $name) {
                $row = DB::table('masterdata')->where('type', 'CASTE')->where('subtype', '0')->whereRaw('lower(name) = ?', [mb_strtolower($name)])->first();
                if ($row) {
                    $dataid = $row->dataid;
                } else {
                    $dataid = (string) (++$next);
                    DB::table('masterdata')->insert(['dataid' => $dataid, 'type' => 'CASTE', 'subtype' => '0', 'abbreviation' => '', 'name' => $name, 'created_at' => $now, 'updated_at' => $now]);
                }
                DB::table('community_countries')->updateOrInsert(
                    ['caste_dataid' => (string) $dataid, 'country_dataid' => (string) $cid],
                    ['exclusive' => $exclusive]
                );
            }
        };

        // ---- home-country lists (replace the common list)
        $add('Afghanistan', ['Pashtun', 'Tajik', 'Hazara', 'Uzbek', 'Turkmen', 'Baloch', 'Nuristani', 'Aymaq', 'Arab', 'Kyrgyz', 'Pashai', 'Brahui', 'Gujjar', 'Qizilbash']);
        $add('Iran', ['Persian', 'Azeri', 'Kurd', 'Lur', 'Baloch', 'Arab', 'Turkmen', 'Gilaki', 'Mazandarani', 'Qashqai', 'Bakhtiari']);
        $add('Iraq', ['Arab', 'Kurd', 'Turkmen', 'Assyrian', 'Yazidi', 'Shabak', 'Mandaean']);
        $add('Turkey', ['Turkish', 'Kurdish', 'Zaza', 'Arab', 'Circassian', 'Laz', 'Albanian', 'Bosniak', 'Georgian']);
        $add('Egypt', ['Egyptian Arab', 'Nubian', 'Bedouin', 'Siwi Berber', 'Beja']);
        $add('Syria', ['Arab', 'Kurd', 'Alawite', 'Druze', 'Turkmen', 'Circassian', 'Assyrian', 'Armenian']);
        $add('Jordan', ['Jordanian', 'Palestinian', 'Circassian', 'Chechen', 'Bedouin', 'Armenian']);
        $add('Lebanon', ['Lebanese Arab', 'Armenian', 'Kurdish', 'Palestinian', 'Syrian']);
        $add('Yemen', ['Arab', 'Afro-Arab', 'Somali', 'Mahri']);
        $add('Sudan', ['Sudanese Arab', 'Fur', 'Beja', 'Nuba', 'Masalit', 'Zaghawa', 'Nubian']);
        $add('Somalia', ['Darod', 'Hawiye', 'Dir', 'Isaaq', 'Rahanweyn', 'Bantu', 'Benadiri']);
        $add('Morocco', ['Arab', 'Amazigh (Berber)', 'Sahrawi', 'Riffian']);
        $add('Algeria', ['Arab', 'Amazigh (Berber)', 'Kabyle', 'Chaoui', 'Tuareg', 'Mozabite']);
        $add('Tunisia', ['Arab', 'Amazigh (Berber)']);
        $add('Libya', ['Arab', 'Amazigh (Berber)', 'Tuareg', 'Tebu']);
        $add('Indonesia', ['Javanese', 'Sundanese', 'Malay', 'Batak', 'Madurese', 'Betawi', 'Minangkabau', 'Bugis', 'Banjar', 'Bantenese', 'Balinese', 'Acehnese', 'Dayak', 'Sasak', 'Chinese']);
        $add('Uzbekistan', ['Uzbek', 'Tajik', 'Kazakh', 'Karakalpak', 'Tatar', 'Russian', 'Kyrgyz']);
        $add('Kazakhstan', ['Kazakh', 'Russian', 'Uzbek', 'Uyghur', 'Tatar', 'German', 'Ukrainian']);
        $add('Tajikistan', ['Tajik', 'Uzbek', 'Pamiri', 'Kyrgyz', 'Russian']);
        $add('Kyrgyzstan', ['Kyrgyz', 'Uzbek', 'Russian', 'Dungan', 'Uyghur']);
        $add('Turkmenistan', ['Turkmen', 'Uzbek', 'Russian', 'Baloch']);
        $add('Azerbaijan', ['Azerbaijani', 'Lezgin', 'Talysh', 'Avar', 'Russian', 'Kurd', 'Georgian']);
        $add('Nigeria', ['Hausa', 'Yoruba', 'Igbo', 'Fulani', 'Kanuri', 'Tiv', 'Ijaw', 'Ibibio', 'Nupe', 'Edo']);
        $add('Ethiopia', ['Oromo', 'Amhara', 'Tigray', 'Somali', 'Sidama', 'Afar', 'Gurage', 'Wolayta']);
        $add('Senegal', ['Wolof', 'Pulaar', 'Serer', 'Mandinka', 'Jola', 'Soninke']);
        $add('Mali', ['Bambara', 'Fulani', 'Soninke', 'Senufo', 'Malinke', 'Dogon', 'Songhai', 'Tuareg']);
        $add('Niger', ['Hausa', 'Zarma-Songhai', 'Tuareg', 'Fulani', 'Kanuri', 'Toubou', 'Arab']);
        $add('Philippines', ['Tagalog', 'Cebuano', 'Ilocano', 'Bisaya', 'Hiligaynon', 'Waray', 'Kapampangan', 'Maguindanao', 'Maranao', 'Tausug']);
        $add('Thailand', ['Thai', 'Thai Malay', 'Lao (Isan)', 'Chinese-Thai', 'Khmer', 'Karen']);
        $add('Bosnia and Herzegovina', ['Bosniak', 'Serb', 'Croat']);
        $add('Albania', ['Albanian', 'Greek', 'Roma']);
        $add('Kosovo', ['Albanian', 'Bosniak', 'Serb', 'Turkish', 'Roma', 'Gorani']);
        $add('Burma', ['Burman', 'Shan', 'Karen', 'Rakhine', 'Rohingya', 'Chin', 'Kachin', 'Mon', 'Kayah']);

        // ---- countries with a large South Asian community: own groups first, then the common list
        $add('Malaysia', ['Malay', 'Chinese', 'Indian', 'Iban', 'Kadazan-Dusun', 'Bidayuh', 'Orang Asli'], false);
        $add('Singapore', ['Chinese', 'Malay', 'Indian', 'Eurasian'], false);
        $add('Kenya', ['Kikuyu', 'Luhya', 'Kalenjin', 'Luo', 'Kamba', 'Somali', 'Kisii', 'Meru', 'Mijikenda', 'Maasai', 'Swahili'], false);
        $add('Tanzania', ['Sukuma', 'Chagga', 'Haya', 'Nyamwezi', 'Makonde', 'Swahili', 'Zanzibari Arab'], false);
        $add('Uganda', ['Baganda', 'Banyankole', 'Basoga', 'Bakiga', 'Iteso', 'Langi', 'Acholi'], false);
        $add('South Africa', ['Zulu', 'Xhosa', 'Afrikaner', 'Cape Malay', 'Coloured', 'Tswana', 'Sotho'], false);
        $add('Mauritius', ['Indo-Mauritian', 'Creole', 'Sino-Mauritian', 'Franco-Mauritian'], false);
        $add('Fiji', ['iTaukei', 'Indo-Fijian', 'Rotuman'], false);
        $add('Trinidad And Tobago', ['Indo-Trinidadian', 'Afro-Trinidadian', 'Mixed (Dougla)'], false);
        $add('Guyana', ['Indo-Guyanese', 'Afro-Guyanese', 'Amerindian'], false);
        $add('Suriname', ['Hindustani', 'Maroon', 'Javanese', 'Creole'], false);
    }

    public function down(): void
    {
        Schema::table('community_countries', function (Blueprint $table) {
            $table->dropColumn('exclusive');
        });
    }
};
