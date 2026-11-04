<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * New accounts must finish the "Complete your profile" wizard before they can use the dashboard. The flag is set at
 * registration only, so existing members are never locked out; it clears itself once the profile reaches 100%.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_complete_profile')->default(false)->after('active');
            $table->timestamp('profile_completed_at')->nullable()->after('must_complete_profile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['must_complete_profile', 'profile_completed_at']);
        });
    }
};
