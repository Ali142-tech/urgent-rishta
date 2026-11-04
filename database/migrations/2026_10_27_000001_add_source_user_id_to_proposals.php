<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An admin can copy a member into the team's proposals ("Move to Team Proposal"). The member the copy came from is
 * remembered here, so pressing the button again opens the same proposal instead of making a duplicate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'source_user_id')) {
                $table->unsignedBigInteger('source_user_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn('source_user_id');
        });
    }
};
