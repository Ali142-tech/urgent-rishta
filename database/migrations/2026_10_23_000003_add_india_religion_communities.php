<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * India's communities for the other religions (Hindu was added earlier): Muslim, Christian, Sikh, Jain and Parsi members living
 * in India now get Indian communities instead of the Pakistani list. Communities that already exist under the religion are
 * linked as they are; the missing ones are added.
 */
return new class extends Migration
{
    public function up(): void
    {
        $india = DB::table('masterdata')->where('type', 'COUNTRY')->where('name', 'India')->value('dataid');
        if (!$india) {
            return;
        }
        $next = (int) DB::table('masterdata')->selectRaw('max(cast(dataid as unsigned)) as m')->value('m');
        $now = now();

        $lists = [
            '739' => ['Ansari', 'Sheikh', 'Syed', 'Pathan', 'Qureshi', 'Mughal', 'Mirza', 'Siddiqui', 'Khan', 'Malik', 'Saifi', 'Salmani', 'Mansoori', 'Idrisi', 'Raeen', 'Memon', 'Khoja', 'Bohra', 'Mappila', 'Labbai', 'Rowther', 'Marakkar', 'Dudekula', 'Pinjara', 'Teli', 'Qassab', 'Rajput (Muslim)', 'Meo', 'Naqvi', 'Hashmi', 'Usmani', 'Farooqi'],
            '741' => ['Roman Catholic', 'Syrian Christian', 'Protestant', 'Latin Catholic', 'Anglo-Indian', 'Mar Thoma', 'Goan Catholic', 'Konkani Catholic', 'Tamil Christian', 'Telugu Christian', 'Malayali Christian', 'Marathi Christian', 'Pentecostal'],
            '742' => ['Jat Sikh', 'Khatri Sikh', 'Arora Sikh', 'Ramgarhia', 'Saini Sikh', 'Mazhabi', 'Ravidasia', 'Labana', 'Kamboj', 'Namdhari', 'Gursikh'],
            '744' => ['Digambar', 'Shwetambar', 'Oswal', 'Porwal', 'Khandelwal', 'Agarwal Jain', 'Bania Jain'],
            '743' => ['Parsi', 'Irani Zoroastrian'],
        ];

        foreach ($lists as $religion => $names) {
            foreach ($names as $name) {
                $row = DB::table('masterdata')->where('type', 'CASTE')->where('subtype', (string) $religion)->whereRaw('lower(name) = ?', [mb_strtolower($name)])->first();
                if ($row) {
                    $dataid = $row->dataid;
                } else {
                    $dataid = (string) (++$next);
                    DB::table('masterdata')->insert(['dataid' => $dataid, 'type' => 'CASTE', 'subtype' => (string) $religion, 'abbreviation' => '', 'name' => $name, 'created_at' => $now, 'updated_at' => $now]);
                }
                DB::table('community_countries')->updateOrInsert(['caste_dataid' => (string) $dataid, 'country_dataid' => (string) $india], ['exclusive' => true]);
            }
        }
    }

    public function down(): void
    {
        // the added communities are ordinary masterdata rows; nothing to undo safely
    }
};
