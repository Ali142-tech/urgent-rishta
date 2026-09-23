<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website Upgrade Brief §3 (Add/Edit Proposal fields) — the only 3 of the
 * brief's listed fields with no backing column anywhere yet. Nationality/
 * Citizenship (con_of_citizenship), Visa/Residence Status
 * (immigration_status), and Family Background (family_residence/
 * family_values) already existed as columns (added earlier for the
 * self-registration flow) but were never wired into the Team "Add/Edit
 * Proposal" form — see TeamController::store()/update() and
 * resources/views/team/proposal-{create,edit}.blade.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'income')) {
                $table->string('income', 50)->nullable()->after('profession');
            }
            if (!Schema::hasColumn('users', 'property_financial_status')) {
                $table->string('property_financial_status', 255)->nullable()->after('income');
            }
            if (!Schema::hasColumn('users', 'profile_description')) {
                $table->text('profile_description')->nullable()->after('property_financial_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter(['income', 'property_financial_status', 'profile_description'], fn ($col) => Schema::hasColumn('users', $col)));
        });
    }
};
