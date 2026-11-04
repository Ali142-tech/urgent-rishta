<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks when a member was last sent their personalized "Weekly Matches"
 * email (see App\Services\WeeklyMatchService) — mirrors
 * last_inactivity_reminder_sent_at's role for the inactivity reminder: lets
 * a DAILY scheduler check determine who's due for a WEEKLY email, without
 * waiting inside a running process.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_weekly_match_email_sent_at')->nullable()->after('last_inactivity_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_weekly_match_email_sent_at');
        });
    }
};
