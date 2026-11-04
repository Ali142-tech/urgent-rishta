<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Upgrade Brief §18. A CollaborationRequest here is "requester
 * asks about ONE proposal," not inherently a pairing of two profiles, so
 * marking a match successful needs the team member to name the
 * counterpart proposal at that point — see TeamController::
 * markSuccessfulMatch(). share_partner_a/b are the brief's "Urgent Rishta
 * 50% / Partner 50%" collaboration split, admin-editable case by case
 * (AdminController::updateSuccessfulMatchShare()) — deliberately kept
 * separate from the AI Match Score, which lives entirely on the
 * CollaborationRequest/Profile::compatibilityWith() side.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('successful_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collaboration_request_id')->nullable();
            $table->unsignedBigInteger('proposal_id');
            $table->unsignedBigInteger('counterpart_proposal_id');
            $table->unsignedBigInteger('partner_a_id');
            $table->unsignedBigInteger('partner_b_id');
            $table->date('matched_at');
            $table->unsignedTinyInteger('share_partner_a')->nullable();
            $table->unsignedTinyInteger('share_partner_b')->nullable();
            $table->timestamps();

            $table->foreign('collaboration_request_id')->references('id')->on('collaboration_requests')->onDelete('set null');
            $table->foreign('proposal_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('counterpart_proposal_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('partner_a_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('partner_b_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('successful_matches');
    }
};
