<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Collaboration Request workflow (request/accept/decline/needs-info
 * gating on team-added proposals) was removed — the whole team-added
 * proposal pool is now open to every active team member directly (see
 * User::canViewContactInfoOf(), TeamController::markSuccessfulMatch()).
 * Drops the successful_matches.collaboration_request_id FK/column first
 * (it referenced this table), then the collaboration_requests table
 * itself.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('successful_matches', function (Blueprint $table) {
            $table->dropForeign(['collaboration_request_id']);
            $table->dropColumn('collaboration_request_id');
        });

        Schema::dropIfExists('collaboration_requests');
    }

    public function down(): void
    {
        Schema::create('collaboration_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('proposal_id');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->unique(['requester_id', 'proposal_id']);
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('proposal_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('successful_matches', function (Blueprint $table) {
            $table->unsignedBigInteger('collaboration_request_id')->nullable();
            $table->foreign('collaboration_request_id')->references('id')->on('collaboration_requests')->onDelete('set null');
        });
    }
};
