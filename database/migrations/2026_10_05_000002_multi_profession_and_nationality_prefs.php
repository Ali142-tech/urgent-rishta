<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Partner requirements: profession becomes a multi-select (pref_professions; pref_profession keeps the
 * same choices as comma-separated text for older screens) and a new multi-select partner nationality.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->json('pref_professions')->nullable();
            $table->json('pref_nationalities')->nullable();   // masterdata COUNTRY dataids
        });
        Schema::table('proposals', function (Blueprint $table) {
            $table->text('pref_profession')->nullable()->change();
        });

        foreach (DB::table('proposals')->whereNotNull('pref_profession')->where('pref_profession', '!=', '')->get(['id', 'pref_profession']) as $row) {
            DB::table('proposals')->where('id', $row->id)->update(['pref_professions' => json_encode([$row->pref_profession])]);
        }
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['pref_professions', 'pref_nationalities']);
        });
    }
};