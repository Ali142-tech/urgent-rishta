<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nothing in this app tracked when a user last logged in — needed to detect
 * "inactive 7+ days" for the automatic reminder email.
 * last_inactivity_reminder_sent_at records when we last emailed a member
 * about being inactive, so the daily check never re-sends it every single
 * day to the same still-inactive member, while still allowing a fresh
 * reminder if they log back in and later go quiet again.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->timestamp('last_inactivity_reminder_sent_at')->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_at', 'last_inactivity_reminder_sent_at']);
        });
    }
};
