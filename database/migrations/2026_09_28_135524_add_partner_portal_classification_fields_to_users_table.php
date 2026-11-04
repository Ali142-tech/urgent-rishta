<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 4 new proposal-classification fields from the client's "AI Partner
 * Portal" mockup (Sep 2026), added to the Add Proposal form:
 * family_status (Middle/Upper Middle/Royal Class — distinct from the
 * existing family_residence [Rent/Own] and family_values [Traditional/
 * Moderate/Liberal]), looking_from (Pakistan/Abroad), profile_category
 * (Standard/Premium/Royal — a purely descriptive team-entered tag, NOT
 * the real paid-membership `package` system self-registered members
 * use), and presentation_highlight (Standard/Highly Attractive — manual-
 * only per the mockup's own stated rule, never auto-filled by the paste
 * parser or any future AI extraction).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('family_status', 30)->nullable()->after('family_values');
            $table->string('looking_from', 20)->nullable()->after('family_status');
            $table->string('profile_category', 20)->nullable()->after('looking_from');
            $table->string('presentation_highlight', 30)->nullable()->after('profile_category');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['family_status', 'looking_from', 'profile_category', 'presentation_highlight']);
        });
    }
};
