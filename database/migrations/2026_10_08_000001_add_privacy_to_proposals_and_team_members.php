<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Private proposals are seen only by admins, their owner and team members the admin allowed.
            $table->boolean('is_private')->default(false)->index()->after('active');
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->boolean('is_private_member')->default(false)->after('can_view_originals');   // everything this member adds is private
            $table->boolean('can_view_private')->default(false)->after('is_private_member');     // may see other people's private proposals
        });
    }

    public function down(): void
    {
        Schema::table('proposals', fn (Blueprint $table) => $table->dropColumn('is_private'));
        Schema::table('team_members', fn (Blueprint $table) => $table->dropColumn(['is_private_member', 'can_view_private']));
    }
};
