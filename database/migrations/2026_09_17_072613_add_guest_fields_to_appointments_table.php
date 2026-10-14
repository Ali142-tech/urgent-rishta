<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The "Book a Private Consultation" homepage popup used to be a static
 * bank-details + WhatsApp box (no DB involved) — it's becoming a real
 * request form that guests (not just logged-in members) can submit, which
 * the existing appointments table wasn't built for: user_id was required.
 * doctrine/dbal isn't installed, so user_id's NOT NULL is dropped via raw
 * SQL rather than Schema::table()->nullable()->change().
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `appointments` MODIFY `user_id` BIGINT UNSIGNED NULL');

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('guest_name', 150)->nullable()->after('user_id');
            $table->string('guest_email', 150)->nullable()->after('guest_name');
            $table->string('guest_phone', 30)->nullable()->after('guest_email');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_email', 'guest_phone']);
        });

        DB::statement('ALTER TABLE `appointments` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');
    }
};
