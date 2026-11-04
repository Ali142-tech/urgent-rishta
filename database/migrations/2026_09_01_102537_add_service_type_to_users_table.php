<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Website Upgrade Brief §6 "Routing rule" — captured at registration so we
            // know whether someone wants to search profiles themselves (online) or
            // have the team find matches for them (personalized). Nullable/nothing
            // enforced yet: existing accounts and any registration that skips this
            // step (e.g. old bookmarked links) simply have no preference on record.
            $table->string('service_type', 20)->nullable()->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
    }
};
