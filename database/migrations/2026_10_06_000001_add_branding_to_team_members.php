<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** A team member's own logo and watermark text, stamped on the proposal photos they share. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('logo', 190)->nullable();
            $table->string('watermark_text', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['logo', 'watermark_text']);
        });
    }
};