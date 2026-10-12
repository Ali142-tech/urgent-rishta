<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            // The letters in front of the number of the proposals this member adds: "P" -> P-199, "UR" -> UR-200 ...
            $table->string('proposal_prefix', 5)->default('P')->after('can_view_private');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', fn (Blueprint $table) => $table->dropColumn('proposal_prefix'));
    }
};