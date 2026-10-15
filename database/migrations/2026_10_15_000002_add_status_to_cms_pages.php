<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Admin-created pages: can be hidden (is_active), listed in the site footer, and the original Terms / Privacy pages are marked as system pages (they cannot be deleted). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('body');
            $table->boolean('is_system')->default(false)->after('is_active');
            $table->boolean('show_in_footer')->default(false)->after('is_system');
            $table->unsignedInteger('sort_order')->default(0)->after('show_in_footer');
        });

        DB::table('cms_pages')->whereIn('slug', ['terms', 'privacy'])->update(['is_system' => true]);
    }

    public function down(): void
    {
        Schema::table('cms_pages', fn (Blueprint $table) => $table->dropColumn(['is_active', 'is_system', 'show_in_footer', 'sort_order']));
    }
};
