<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Single-row settings table (see MatchWeightSetting for the identical
 * pattern) — which contact fields actually render once
 * User::canViewContactInfoOf() passes. All default true so nothing
 * changes for anyone until an admin actually unchecks something; the
 * gate itself (has this person earned access AT ALL) stays entirely on
 * canViewContactInfoOf() — this table only prunes which of the four
 * fields show once that gate is already open.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_unlock_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('unlock_email')->default(true);
            $table->boolean('unlock_phone')->default(true);
            $table->boolean('unlock_name')->default(true);
            $table->boolean('unlock_address')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_unlock_settings');
    }
};
