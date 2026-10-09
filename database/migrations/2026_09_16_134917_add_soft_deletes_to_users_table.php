<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Delete Profile" used to be a real, permanent delete (and cascaded to
 * hard-delete Images/Interest/Filtered too) — no way back if clicked by
 * mistake (see the admin-account incident this same session). Adding
 * Laravel's standard softDeletes() column here: App\User gets the
 * SoftDeletes trait, which makes $user->delete() set this column instead
 * of removing the row, and automatically excludes soft-deleted users from
 * every Eloquent query (login, retrieveUserObject, etc.) via a global
 * scope. The raw-SQL Profile::profiles() query (used for search/homepage/
 * admin list) doesn't go through Eloquent, so it's guarded separately.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
