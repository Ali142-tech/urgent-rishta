<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail for admin photo-verification decisions (approve / reject /
 * reopen). The `users` table only ever holds the *current* state
 * (photo_verification_status, photo_verified_by, photo_rejection_reason) —
 * every time it changes, the previous decision (who, when, why) is
 * overwritten with no history. This table keeps one permanent row per
 * decision instead, so "who approved/rejected which member and when" can
 * actually be looked up later.
 *
 * dataid columns are denormalized alongside the numeric ids on purpose:
 * the admin UI looks members up by dataid everywhere, and a log entry
 * should still be readable even if the referenced user is later deleted —
 * so these aren't hard foreign keys.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_verification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('user_dataid', 20)->index();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('admin_dataid', 20)->nullable();
            $table->enum('action', ['approved', 'rejected', 'reopened']);
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['user_dataid', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_verification_logs');
    }
};
