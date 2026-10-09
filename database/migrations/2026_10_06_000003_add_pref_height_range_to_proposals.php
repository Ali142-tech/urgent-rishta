<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Partner height as a range (inches). Null = no limit on that side. pref_height keeps the same range as text for older screens. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->unsignedTinyInteger('pref_height_min')->nullable();
            $table->unsignedTinyInteger('pref_height_max')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['pref_height_min', 'pref_height_max']);
        });
    }
};