<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Marital status list requested by the client: Never Married (Single), Married, Divorced, Khula Taken (Khula-Fied), Widowed,
 * Separated, Nikah Only (No Rukhsati), Annulled.
 * Members store the option's dataid, so renaming "Never Married" keeps every existing profile intact. "Awaiting Divorce" is left
 * alone because existing members may have chosen it.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        DB::table('masterdata')->where('type', 'MARITAL_STATUS')->where('name', 'Never Married')
            ->update(['name' => 'Never Married (Single)', 'updated_at' => $now]);

        $next = (int) DB::table('masterdata')->selectRaw('max(cast(dataid as unsigned)) as m')->value('m');
        foreach (['Khula Taken (Khula-Fied)', 'Separated', 'Nikah Only (No Rukhsati)'] as $name) {
            if (DB::table('masterdata')->where('type', 'MARITAL_STATUS')->where('name', $name)->exists()) {
                continue;
            }
            DB::table('masterdata')->insert([
                'dataid' => (string) (++$next), 'type' => 'MARITAL_STATUS', 'subtype' => '', 'abbreviation' => '',
                'name' => $name, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('masterdata')->where('type', 'MARITAL_STATUS')->whereIn('name', ['Khula Taken (Khula-Fied)', 'Separated', 'Nikah Only (No Rukhsati)'])->delete();
        DB::table('masterdata')->where('type', 'MARITAL_STATUS')->where('name', 'Never Married (Single)')->update(['name' => 'Never Married']);
    }
};
