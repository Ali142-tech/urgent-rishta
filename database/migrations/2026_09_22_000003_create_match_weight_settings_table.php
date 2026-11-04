<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Single-row settings table — admin-configurable weight per compatibility
 * factor (see Profile::compatibilityWith()). Default 1 for every factor
 * reproduces today's flat-average behavior exactly (equal weights), so
 * this migration changes nothing about existing scores until an admin
 * actually edits a value.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_weight_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('age_weight')->default(1);
            $table->unsignedInteger('location_weight')->default(1);
            $table->unsignedInteger('religion_weight')->default(1);
            $table->unsignedInteger('caste_weight')->default(1);
            $table->unsignedInteger('marital_status_weight')->default(1);
            $table->unsignedInteger('education_weight')->default(1);
            $table->unsignedInteger('mother_tongue_weight')->default(1);
            $table->unsignedInteger('children_weight')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_weight_settings');
    }
};
