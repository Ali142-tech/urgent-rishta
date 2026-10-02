<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Matchmakers (team members) get their own table AND their own login — they
 * are no longer rows in `users`. A team member signs in at /team/login (the
 * `team` auth guard) and never touches the member side of the site.
 *
 * The five matchmaker columns that used to sit on `users` are dropped. Team
 * members that existed there are NOT copied across (start fresh): their old
 * `users` rows are soft-deleted so they can no longer sign in as members, and
 * the matchmakers re-apply through "Become a Partner".
 *
 * Runs before the proposals migration, which points successful_matches at
 * this table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('dataid', 20)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->string('contact_mobile_number', 30);
            $table->string('city', 100)->nullable();
            $table->string('password');
            $table->string('photo', 190)->nullable();
            $table->rememberToken();

            $table->boolean('is_admin')->default(false);                    // an admin's own team account (see AdminTeamMemberSeeder)
            $table->boolean('is_approved')->default(false)->index();
            $table->string('status', 20)->default('active');                 // active / suspended / deactivated
            $table->string('application_status', 20)->default('pending')->index(); // pending / approved / rejected
            $table->string('experience', 255)->nullable();
            $table->text('about_me')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        if (Schema::hasColumn('users', 'is_team_member')) {
            DB::table('users')
                ->where(function ($q) {
                    $q->where('is_team_member', 1)->orWhereNotNull('matchmaker_status');
                })
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['is_team_member', 'team_member_status', 'matchmaker_status', 'experience', 'about_me']);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'is_team_member')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_team_member')->default(false)->after('admin');
                $table->string('team_member_status', 20)->default('active')->after('is_team_member');
                $table->string('matchmaker_status', 20)->nullable()->after('team_member_status');
                $table->string('experience', 255)->nullable()->after('matchmaker_status');
                $table->text('about_me')->nullable()->after('experience');
            });
        }

        Schema::dropIfExists('team_members');
    }
};
