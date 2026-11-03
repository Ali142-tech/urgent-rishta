<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Temporary access accounts for the Team Dashboard: an admin creates one, the person signs in with the email + password the
 * admin sends, and the clock (temp_minutes) starts at their FIRST sign-in. When it runs out the account is finished for good.
 * An account that is never used stops working at temp_link_expires_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->boolean('is_temporary')->default(false)->index()->after('is_admin');
            $table->unsignedSmallInteger('temp_minutes')->nullable()->after('is_temporary');   // 15 or 30
            $table->timestamp('temp_first_login_at')->nullable()->after('temp_minutes');
            $table->timestamp('temp_expires_at')->nullable()->after('temp_first_login_at');    // set at the first sign-in
            $table->timestamp('temp_link_expires_at')->nullable()->after('temp_expires_at');   // the sign-in details stop working if never used
            $table->unsignedBigInteger('temp_created_by')->nullable()->after('temp_link_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['is_temporary', 'temp_minutes', 'temp_first_login_at', 'temp_expires_at', 'temp_link_expires_at', 'temp_created_by']);
        });
    }
};
