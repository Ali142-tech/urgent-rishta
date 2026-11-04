<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clicks from UrgentRishta to partner websites (Gallery ▸ Our Partners ▸ /go/{partner}). Only what the Partner Referral
 * report needs: which partner, when, who (a signed-in member / team member id, otherwise just an anonymous, salted hash of
 * the browser session — no IP address, no raw session id) and where the visitor was sent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('partner_key', 30);
            $table->string('partner_name', 100);
            $table->unsignedBigInteger('user_id')->nullable();          // signed-in site member (users.id)
            $table->unsignedBigInteger('team_member_id')->nullable();   // signed-in team member (team_members.id)
            $table->string('visitor_hash', 16)->nullable();             // HMAC of the session id — groups clicks of one browser session, not a person
            $table->string('destination_url', 500);
            $table->string('referral_source', 40)->default('urgentrishta');
            $table->string('utm_source', 60)->nullable();
            $table->string('utm_medium', 60)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['partner_key', 'created_at']);
            $table->index('created_at');
            $table->index('user_id');
            $table->index('team_member_id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_clicks');
    }
};
