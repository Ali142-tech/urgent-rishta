<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Generic audit trail — every Log::info(...) call elsewhere in this app
 * only ever reaches storage/logs/laravel.log (write-only, nothing reads it
 * back). This table is queryable and admin-visible (see
 * AdminController::auditLog()). `subject_type`/`subject_id` are a plain
 * polymorphic pair (not Eloquent's built-in morphs helper, since nothing
 * here needs Eloquent's morph-map magic) so an entry can point at whatever
 * the action was about (a proposal, a collaboration request, ...) without
 * a separate column per subject type.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action', 60);
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('actor_id')->references('id')->on('users')->onDelete('set null');
            $table->index('action');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
