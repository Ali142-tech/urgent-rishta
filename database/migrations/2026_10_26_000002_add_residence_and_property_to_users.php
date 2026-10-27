<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Residence & Property" details collected in the Complete Your Profile form. Area / society (`society`), the residential address
 * (`address`), the house size (`residence_size`), the living arrangement and the family financial status (`family_status`) already
 * have columns; these are the new ones. None of them is shown on a public profile.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('residence_type', 60)->nullable();
            $table->string('property_ownership', 60)->nullable();
            $table->string('hometown', 100)->nullable();
            $table->string('permanent_country', 20)->nullable();
            $table->string('additional_properties', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['residence_type', 'property_ownership', 'hometown', 'permanent_country', 'additional_properties']);
        });
    }
};
