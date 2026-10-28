<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A member copied by the admin ("Move to Team Proposal") is a draft until the admin presses Save on the edit page:
 * drafts are hidden from every proposal list, search and match (see PrivateProposalScope).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'is_draft')) {
                $table->boolean('is_draft')->default(false)->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn('is_draft');
        });
    }
};
