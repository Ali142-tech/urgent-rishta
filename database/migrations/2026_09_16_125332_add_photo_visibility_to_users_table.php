<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Admin-controlled override for how a member's photo appears to OTHER
 * members — independent of photo_verification_status (which is about
 * whether the photo was approved at all) and independent of the viewer's
 * own auth state (App\Profile::showBlur() already blurs for guests).
 * 'visible' (default) changes nothing; 'blurred' forces a blur even for
 * logged-in viewers; 'hidden' shows the generic gender avatar to everyone.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_visibility', 10)->default('visible')->after('photo_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo_visibility');
        });
    }
};
