<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The rest of the client intake form (religion, income, residence, family and partner-preference extras) as real
 * columns, so the complete form is stored — not only inside the pasted text. All optional.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('religion', 100)->nullable();
            $table->string('monthly_income', 100)->nullable();
            $table->string('residential_area', 200)->nullable();
            $table->string('house_size', 100)->nullable();
            $table->string('father_details', 200)->nullable();
            $table->string('mother_details', 200)->nullable();
            $table->string('siblings', 100)->nullable();
            $table->string('married_siblings', 100)->nullable();
            $table->string('family_values', 60)->nullable();
            $table->string('pref_languages', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['religion', 'monthly_income', 'residential_area', 'house_size', 'father_details', 'mother_details', 'siblings', 'married_siblings', 'family_values', 'pref_languages']);
        });
    }
};