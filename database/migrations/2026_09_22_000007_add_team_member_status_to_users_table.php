<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `is_team_member` (binary) already IS the approve/revoke step (originally
 * AdminController::toggleTeamMember(), now AdminController::
 * approveMatchmakerApplication()/rejectMatchmakerApplication()). This adds
 * the orthogonal lifecycle dimension the brief also wants — suspend/
 * deactivate/reactivate an ALREADY-approved partner without revoking the
 * role itself, so reactivating doesn't require re-granting it from
 * scratch. Same plain string-column shape as profile_status/
 * photo_verification_status.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'team_member_status')) {
                $table->string('team_member_status', 20)->default('active')->after('is_team_member');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'team_member_status')) {
                $table->dropColumn('team_member_status');
            }
        });
    }
};
