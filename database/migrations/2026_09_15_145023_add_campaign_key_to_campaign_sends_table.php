<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * campaign_sends was originally keyed unique on (dataid, gender) — fine while
 * the only campaign was the gendered reactivation email, since one user only
 * ever has one gender. Adding a second, differently-purposed campaign
 * (profile completion) through the same table would otherwise collide: a
 * user who already has a gender-campaign row would look "already handled"
 * for any new campaign sharing that same gender value.
 *
 * campaign_key decouples the two: existing rows are backfilled as
 * 'gender_reactivation', new campaigns use their own key, and the unique
 * constraint moves to (dataid, campaign_key) so each campaign tracks its own
 * per-user send state independently.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_sends', function (Blueprint $table) {
            $table->string('campaign_key', 40)->nullable()->after('gender');
        });

        DB::table('campaign_sends')->whereNull('campaign_key')->update(['campaign_key' => 'gender_reactivation']);

        Schema::table('campaign_sends', function (Blueprint $table) {
            $table->string('campaign_key', 40)->nullable(false)->default('gender_reactivation')->change();
            $table->dropUnique(['dataid', 'gender']);
            $table->unique(['dataid', 'campaign_key']);
        });
    }

    public function down(): void
    {
        Schema::table('campaign_sends', function (Blueprint $table) {
            $table->dropUnique(['dataid', 'campaign_key']);
            $table->unique(['dataid', 'gender']);
            $table->dropColumn('campaign_key');
        });
    }
};
