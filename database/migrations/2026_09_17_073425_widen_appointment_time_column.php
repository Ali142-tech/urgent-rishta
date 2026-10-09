<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The new consultation-request time slots ("Afternoon (12pm - 4pm)", 22
 * chars) don't fit the original 20-char limit — widen it rather than
 * shorten the labels. Raw SQL since doctrine/dbal isn't installed.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `appointments` MODIFY `appointment_time` VARCHAR(40) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `appointments` MODIFY `appointment_time` VARCHAR(20) NULL');
    }
};
