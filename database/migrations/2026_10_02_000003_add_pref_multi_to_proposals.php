<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Partner requirements the team can pick several values for: caste, education, marital status (all optional). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->json('pref_castes')->nullable();            // proposal_castes ids
            $table->json('pref_educations')->nullable();        // masterdata EDUCATION dataids
            $table->json('pref_marital_statuses')->nullable();  // masterdata MARITAL_STATUS dataids
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['pref_castes', 'pref_educations', 'pref_marital_statuses']);
        });
    }
};
