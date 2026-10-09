<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The previous migration added `photo_verification_status` with a
     * DEFAULT of 'pending', which MySQL backfills onto every existing row —
     * so every user who registered before this feature existed suddenly
     * looked like a fresh signup awaiting review. This migration runs
     * immediately after that one (before any real traffic hits the new
     * registration flow), so every 'pending' row at this point is a legacy
     * account, not a genuine new-signup waiting in the queue. Grandfather
     * them as 'verified' so the admin queue only ever shows real new
     * registrations going forward.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('photo_verification_status', 'pending')
            ->update(['photo_verification_status' => 'verified']);
    }

    public function down(): void
    {
        // One-way data backfill — nothing sensible to revert to.
    }
};
