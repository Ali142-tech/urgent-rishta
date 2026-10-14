<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Public "Become a Partner" matchmaker signup (client request, Sep 2026) —
 * creates a normal `users` row (same table as everyone else, see
 * MatchmakerApplicationController's own docblock for why), just tagged as
 * a pending matchmaker application until an admin approves it.
 *
 * matchmaker_status is nullable and NEVER set for a normal self-
 * registered member or a team-added proposal — its mere presence is what
 * distinguishes "this row came from the matchmaker signup form" from
 * everyone else, independent of is_team_member (which only reflects
 * whether they've actually been APPROVED for Team Dashboard access).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('matchmaker_status', 20)->nullable()->after('team_member_status');
            $table->string('experience', 255)->nullable()->after('matchmaker_status');
            $table->text('about_me')->nullable()->after('experience');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['matchmaker_status', 'experience', 'about_me']);
        });
    }
};
