<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "AI Partner Portal" mockup's "Original data protection" promise (Sep
 * 2026) — the exact text a team member pastes into the Add Proposal form
 * is preserved here, unedited, separate from the structured fields
 * parsed out of it. Only ever written once, at creation
 * (TeamController::store()) — never overwritten on edit, so it stays a
 * true "original submission" record.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('raw_intake_text')->nullable()->after('presentation_highlight');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('raw_intake_text');
        });
    }
};
