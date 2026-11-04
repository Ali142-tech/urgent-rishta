<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Website Upgrade Brief §5 — photo verification audit trail
     * (Pending → Verified → Rejected/Resubmit) and distinguishing the
     * live-captured selfie from the two regular uploaded photos so an
     * admin can compare them side by side.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_verification_status', 20)->default('pending')->after('service_type');
            $table->timestamp('photo_verified_at')->nullable()->after('photo_verification_status');
            $table->unsignedBigInteger('photo_verified_by')->nullable()->after('photo_verified_at');
            $table->text('photo_rejection_reason')->nullable()->after('photo_verified_by');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->boolean('is_selfie')->default(0)->after('displaypic');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo_verification_status', 'photo_verified_at', 'photo_verified_by', 'photo_rejection_reason']);
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('is_selfie');
        });
    }
};
