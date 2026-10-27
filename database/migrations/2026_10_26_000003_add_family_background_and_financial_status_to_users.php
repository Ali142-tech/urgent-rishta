<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Family Background & Financial Status" details of the Complete Your Profile form. The family financial status uses the existing
 * `family_status` column; these are the new ones. They are used for matching and by the relationship managers and are not shown
 * on a public profile.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('family_background', 60)->nullable();
            $table->string('family_business', 5)->nullable();
            $table->string('family_business_category', 60)->nullable();
            $table->string('family_living_standard', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['family_background', 'family_business', 'family_business_category', 'family_living_standard']);
        });
    }
};
