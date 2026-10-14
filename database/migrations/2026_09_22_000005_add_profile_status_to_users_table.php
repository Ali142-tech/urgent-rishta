<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Team-added proposal lifecycle status (Website Upgrade Brief §17) —
 * separate from the existing boolean `active` flag. Plain string column,
 * same shape as `photo_verification_status`. Meaningful/settable only for
 * proposals (added_by IS NOT NULL) — see TeamController::updateProfileStatus().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_status')) {
                $table->string('profile_status', 20)->default('active')->after('added_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_status')) {
                $table->dropColumn('profile_status');
            }
        });
    }
};
