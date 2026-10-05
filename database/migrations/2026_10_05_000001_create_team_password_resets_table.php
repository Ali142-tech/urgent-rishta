<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Password-reset tokens for team members (kept apart from the member side's `password_resets`). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_password_resets', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_password_resets');
    }
};
