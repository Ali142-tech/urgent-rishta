<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Client sent a real WhatsApp intake template used to collect proposal
 * details from prospective clients, and asked for the Add Proposal form
 * to match it field-for-field (see TeamController::store()). Several of
 * its fields have no home in the existing schema — added here as plain
 * nullable columns, all team-added-proposal-specific (self-registration
 * is untouched).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'college_university')) {
                $table->text('college_university')->nullable()->after('education');
            }
            if (!Schema::hasColumn('users', 'residence_size')) {
                $table->string('residence_size', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'current_city')) {
                $table->string('current_city', 150)->nullable()->after('residence_size');
            }
            if (!Schema::hasColumn('users', 'father_occupation')) {
                $table->string('father_occupation', 255)->nullable()->after('family_values');
            }
            if (!Schema::hasColumn('users', 'mother_occupation')) {
                $table->string('mother_occupation', 255)->nullable()->after('father_occupation');
            }
            if (!Schema::hasColumn('users', 'siblings_brothers')) {
                $table->text('siblings_brothers')->nullable()->after('mother_occupation');
            }
            if (!Schema::hasColumn('users', 'siblings_sisters')) {
                $table->text('siblings_sisters')->nullable()->after('siblings_brothers');
            }
            if (!Schema::hasColumn('users', 'siblings_married_note')) {
                $table->string('siblings_married_note', 255)->nullable()->after('siblings_sisters');
            }
        });

        Schema::table('partner_preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('partner_preferences', 'pref_height')) {
                $table->string('pref_height', 100)->nullable();
            }
            if (!Schema::hasColumn('partner_preferences', 'pref_city')) {
                $table->string('pref_city', 150)->nullable();
            }
            if (!Schema::hasColumn('partner_preferences', 'pref_caste_note')) {
                $table->string('pref_caste_note', 255)->nullable();
            }
            if (!Schema::hasColumn('partner_preferences', 'pref_qualification_note')) {
                $table->text('pref_qualification_note')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['college_university', 'residence_size', 'current_city', 'father_occupation', 'mother_occupation', 'siblings_brothers', 'siblings_sisters', 'siblings_married_note']);
        });

        Schema::table('partner_preferences', function (Blueprint $table) {
            $table->dropColumn(['pref_height', 'pref_city', 'pref_caste_note', 'pref_qualification_note']);
        });
    }
};
