<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-recipient status for the gender email campaign (SendCampaignEmailJob).
 * One row per (user, gender) — created when a job is dispatched, updated by
 * the job itself. This is what makes the campaign command resumable: a
 * dispatch run only queues rows that aren't already 'sent', so restarting
 * the whole command after a partial run (or after the process was killed)
 * never re-emails anyone who already got it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_sends', function (Blueprint $table) {
            $table->id();
            $table->string('dataid', 20)->index();
            $table->string('gender', 10);
            $table->string('email');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['dataid', 'gender']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_sends');
    }
};
