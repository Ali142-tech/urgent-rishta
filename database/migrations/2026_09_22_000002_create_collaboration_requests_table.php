<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per (requester, proposal) pair — a team member ("Partner A")
 * asking to collaborate on a specific proposal another team member
 * ("Partner B", derived via `proposal->added_by`) added. Mirrors
 * photo_access_requests' request/accept-or-decline/withdraw shape, plus a
 * 4th status for "Need More Information" (photo access only needed 3).
 * Unlike photo access — where the "owner" and the "thing being asked
 * about" are the same person (their own hidden photos) — here the
 * requested-about thing is a specific proposal, and a single owning team
 * member can have many proposals each independently requested by many
 * different partners, so the pair is (requester, proposal), not
 * (requester, owner).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaboration_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('proposal_id');
            $table->tinyInteger('status')->default(0); // 0=pending, 1=accepted, -1=declined, 2=needs_info
            $table->timestamps();

            $table->unique(['requester_id', 'proposal_id']);
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('proposal_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaboration_requests');
    }
};
