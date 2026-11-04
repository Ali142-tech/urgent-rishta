<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Team Member role: admin can promote any user to a "Team Member" who can
 * manually add proposals on a client's behalf. `added_by` is null for every
 * normal self-registered profile, and set to the acting team member's user
 * id for one they added — this is the single discriminator the Team
 * Dashboard and the regular-pool exclusion (HomeController::search()/
 * User::recommendedMatchesWhere()) both filter on.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_team_member')) {
                $table->boolean('is_team_member')->default(false)->after('admin');
            }
            if (!Schema::hasColumn('users', 'added_by')) {
                $table->unsignedBigInteger('added_by')->nullable()->after('is_team_member');
                $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
                $table->index('added_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'added_by')) {
                $table->dropForeign(['added_by']);
                $table->dropColumn('added_by');
            }
            if (Schema::hasColumn('users', 'is_team_member')) {
                $table->dropColumn('is_team_member');
            }
        });
    }
};
