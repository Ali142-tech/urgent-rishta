<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Request to view hidden photos" — a member with a Private (hidden)
     * photo can grant a specific other member access to it, mirroring the
     * existing `interest` table's send/accept/decline/withdraw shape (see
     * ProfileController::sendInterest() etc.) — `uid` is the requester,
     * `pid` the photo owner being asked, `allowed` is 0=requested,
     * 1=granted, -1=declined. One row per (requester, owner) pair; a
     * withdrawn request is deleted outright rather than kept as a 4th
     * state, same as Interest::withdrawInterest() does for the sender side.
     *
     * This table (and the admin/member UI referencing `uid`/`pid`/`allowed`)
     * was already scaffolded — App\Notifications\PhotoAccess{Requested,
     * Granted,Declined} and resources/views/admin/dashboard/photoaccess*
     * / resources/views/member/photoaccessdata.blade.php all predate this
     * migration but had no backing table or routes; this finishes it.
     */
    public function up(): void
    {
        Schema::create('photo_access_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid');
            $table->unsignedBigInteger('pid');
            $table->tinyInteger('allowed')->default(0);
            $table->timestamps();

            $table->unique(['uid', 'pid']);
            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pid')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_access_requests');
    }
};
