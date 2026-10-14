<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remembers that an .ics calendar invite email went out for this
 * appointment, so a later cancel/reopen knows to send the matching
 * METHOD:CANCEL (and doesn't email a cancellation for an invite that was
 * never sent).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('calendar_invite_sent_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('calendar_invite_sent_at');
        });
    }
};
