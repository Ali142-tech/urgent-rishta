<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * `users`.`siblings` was `varchar(5)` from its original legacy definition
     * (never had a real edit form). The add_family_background_fields_to_users_table
     * migration repurposed it for "Married Siblings" free text — but the My
     * Profile form's own placeholder ("e.g. 1 brother married", 22 chars)
     * already overflows 5 characters, so anything typed there was being
     * silently truncated on save. Widening it, not adding a new column —
     * same repurposed field, just enough room for what it's actually used for.
     *
     * Raw SQL (not ->change()) — doctrine/dbal isn't installed in this project.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `users` MODIFY `siblings` VARCHAR(100) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `users` MODIFY `siblings` VARCHAR(5) NULL');
    }
};
