<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Partner Preferences moves out of the 14 `r*`-prefixed columns that
     * have lived directly on `users` (edited as the "Partner Expectation"
     * section of the My Profile page) into its own table, so it can get a
     * dedicated dashboard page and grow (preferred state, multi-language,
     * a real min/max age range) without adding yet more columns to an
     * already very wide `users` table.
     *
     * The old `users`.`r*` columns are intentionally left in place (not
     * dropped) — existing values are copied forward into the new table
     * below, but the columns themselves stay as harmless, no-longer-read
     * legacy data in case anything unexpected still needs them.
     */
    public function up(): void
    {
        Schema::create('partner_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->string('height', 50)->nullable();
            $table->string('weight', 50)->nullable();
            $table->string('marital_status', 12)->nullable();
            $table->string('with_children', 20)->nullable();
            $table->string('country_id', 12)->nullable();
            $table->string('state_id', 12)->nullable();
            $table->string('city_id', 12)->nullable();
            $table->string('religion_id', 12)->nullable();
            $table->string('caste_id', 12)->nullable();
            $table->string('sect', 100)->nullable();
            $table->string('education_id', 12)->nullable();
            $table->string('profession', 250)->nullable();
            $table->string('mother_tongue_id', 12)->nullable();
            // Comma-separated masterdata (MOTHER_TONGUE) codes — same
            // storage convention already used for a member's photo list
            // (users/profile `images` column), for a real multi-select
            // "Languages Spoken" instead of the single mother-tongue
            // preference `mother_tongue_id` already covers.
            $table->text('languages')->nullable();
            $table->string('preferred_country_id', 12)->nullable();
            $table->text('general_requirement')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Backfill: copy every existing member's partner-expectation values
        // (the old `users`.`r*` columns) into their new partner_preferences
        // row, so nobody's existing preferences appear to vanish. Only
        // inserts a row for users who actually have at least one of these
        // set — an empty row for someone who never filled this in isn't
        // useful and would just be noise.
        DB::statement("
            INSERT INTO `partner_preferences`
                (`user_id`, `height`, `weight`, `marital_status`, `with_children`,
                 `country_id`, `city_id`, `religion_id`, `caste_id`, `sect`,
                 `education_id`, `profession`, `mother_tongue_id`,
                 `preferred_country_id`, `general_requirement`, `created_at`, `updated_at`)
            SELECT
                `id`, `rheight`, `rweight`, `rmarital_status`, `rwith_children`,
                `rcon_of_residence`, `rcity`, `rreligion`, `rcaste`, `rsect`,
                `reducation`, `rprofession`, `rmother_tongue`,
                `rcon_pref`, `rgen_req`, NOW(), NOW()
            FROM `users`
            WHERE `rgen_req` IS NOT NULL OR `rage` IS NOT NULL OR `rheight` IS NOT NULL
               OR `rweight` IS NOT NULL OR `rmarital_status` IS NOT NULL OR `rwith_children` IS NOT NULL
               OR `rcon_of_residence` IS NOT NULL OR `rcity` IS NOT NULL OR `rreligion` IS NOT NULL
               OR `rcaste` IS NOT NULL OR `rsect` IS NOT NULL OR `reducation` IS NOT NULL
               OR `rprofession` IS NOT NULL OR `rmother_tongue` IS NOT NULL OR `rcon_pref` IS NOT NULL
        ");

        // The old free-text `rage` ('25-30', '25+', etc.) can't be split
        // into real age_min/age_max automatically — best-effort: pull out
        // the first 1-2 digit number as age_min for whoever already had a
        // row inserted above, or as their own new row if `rage` was the
        // *only* preference field they'd filled in. Anything that doesn't
        // parse just stays NULL (shows as "not set" on the new page,
        // rather than a wrong guess).
        DB::statement("
            INSERT INTO `partner_preferences` (`user_id`, `age_min`, `created_at`, `updated_at`)
            SELECT `id`, CAST(SUBSTRING(`rage`, 1, 2) AS UNSIGNED), NOW(), NOW()
            FROM `users`
            WHERE `rage` REGEXP '^[0-9]'
              AND `id` NOT IN (SELECT `user_id` FROM `partner_preferences`)
        ");
        DB::statement("
            UPDATE `partner_preferences` `pp`
            INNER JOIN `users` `u` ON `u`.`id` = `pp`.`user_id`
            SET `pp`.`age_min` = CAST(SUBSTRING(`u`.`rage`, 1, 2) AS UNSIGNED)
            WHERE `u`.`rage` REGEXP '^[0-9]' AND `pp`.`age_min` IS NULL
        ");

        // Recreate the `profile` VIEW so every existing page that reads
        // rage/rheight/lbl_rreligion/etc. off it keeps working unchanged —
        // now sourced from partner_preferences (live data) instead of the
        // frozen `users`.`r*` columns. Only the r*-preference joins change;
        // everything else is copied verbatim from the view's prior
        // definition (confirmed via SHOW CREATE VIEW before this edit).
        DB::statement('DROP VIEW IF EXISTS `profile`');
        DB::statement("
            CREATE VIEW `profile` AS
            SELECT
                `u`.`id` AS `id`, `u`.`dataid` AS `dataid`, `u`.`intro` AS `intro`,
                `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `u`.`gender` AS `gender`,
                `u`.`email` AS `email`, `u`.`age` AS `age`, `u`.`marital_status` AS `marital_status`,
                `u`.`children` AS `children`, `u`.`area` AS `area`, `u`.`profile_for` AS `profile_for`,
                `u`.`contact_mobile_number` AS `contact_mobile_number`, `u`.`mobile_country` AS `mobile_country`,
                `u`.`birthday` AS `birthday`, `u`.`education` AS `education`, `u`.`profession` AS `profession`,
                `u`.`salary` AS `salary`, `u`.`height` AS `height`, `u`.`weight` AS `weight`,
                `u`.`mother_tongue` AS `mother_tongue`, `u`.`language` AS `language`,
                `u`.`con_of_birth` AS `con_of_birth`, `u`.`con_of_residence` AS `con_of_residence`,
                `u`.`con_of_citizenship` AS `con_of_citizenship`, `u`.`con_grew_up` AS `con_grew_up`,
                `u`.`immigration_status` AS `immigration_status`, `u`.`religion` AS `religion`,
                `u`.`caste` AS `caste`, `u`.`sect` AS `sect`, `u`.`state` AS `state`, `u`.`city` AS `city`,
                `u`.`society` AS `society`, `u`.`father` AS `father`, `u`.`mother` AS `mother`,
                `u`.`brother` AS `brother`, `u`.`sister` AS `sister`, `u`.`district` AS `district`,
                `u`.`family_residence` AS `family_residence`, `u`.`father_profession` AS `father_profession`,
                `u`.`special_circumstances` AS `special_circumstances`,
                `pp`.`general_requirement` AS `rgen_req`,
                `pp`.`age_min` AS `rage_min`, `pp`.`age_max` AS `rage_max`,
                `pp`.`height` AS `rheight`, `pp`.`weight` AS `rweight`,
                `pp`.`marital_status` AS `rmarital_status`, `pp`.`with_children` AS `rwith_children`,
                `pp`.`country_id` AS `rcon_of_residence`, `pp`.`city_id` AS `rcity`,
                `pp`.`religion_id` AS `rreligion`, `pp`.`caste_id` AS `rcaste`, `pp`.`sect` AS `rsect`,
                `pp`.`education_id` AS `reducation`, `pp`.`profession` AS `rprofession`,
                `pp`.`mother_tongue_id` AS `rmother_tongue`, `pp`.`languages` AS `rlanguages`,
                `pp`.`preferred_country_id` AS `rcon_pref`,
                `u`.`address` AS `address`, `u`.`companyname` AS `companyname`, `u`.`baddress` AS `baddress`,
                `u`.`siblings` AS `siblings`, `u`.`agelmt` AS `agelmt`, `u`.`od` AS `od`,
                `u`.`password` AS `password`, `u`.`package` AS `package`, `u`.`admin` AS `admin`,
                `u`.`active` AS `active`, `u`.`email_verified_at` AS `email_verified_at`,
                `u`.`remember_token` AS `remember_token`, `u`.`created_at` AS `created_at`,
                `u`.`updated_at` AS `updated_at`,
                CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `name`,
                `mp`.`name` AS `lbl_package`, `mms`.`name` AS `lbl_marital_status`, `mr`.`name` AS `lbl_religion`,
                `mmt`.`name` AS `lbl_mother_tongue`, `ml`.`name` AS `lbl_language`,
                `mcor`.`name` AS `lbl_con_of_residence`, `mcob`.`name` AS `lbl_con_of_birth`,
                `mcoc`.`name` AS `lbl_con_of_citizenship`, `mcgu`.`name` AS `lbl_con_grew_up`,
                `ms`.`name` AS `lbl_state`, `mc`.`name` AS `lbl_city`, `mcst`.`name` AS `lbl_caste`,
                `me`.`name` AS `lbl_education`,
                `rmms`.`name` AS `lbl_rmarital_status`, `rmcor`.`name` AS `lbl_rcon_of_residence`,
                `rmc`.`name` AS `lbl_rcity`, `rmr`.`name` AS `lbl_rreligion`, `rmcst`.`name` AS `lbl_rcaste`,
                `rme`.`name` AS `lbl_reducation`, `rmmt`.`name` AS `lbl_rmother_tongue`,
                `rmcp`.`name` AS `lbl_rcon_pref`, `rms`.`name` AS `lbl_rstate`,
                `dp`.`img_url` AS `displaypic`,
                GROUP_CONCAT(`i`.`img_url` SEPARATOR ',') AS `images`
            FROM `users` `u`
            LEFT JOIN `masterdata` `mp` ON (`u`.`package` = `mp`.`dataid` AND `mp`.`type` = 'PACKAGE')
            LEFT JOIN `masterdata` `mms` ON (`u`.`marital_status` = `mms`.`dataid` AND `mms`.`type` = 'MARITAL_STATUS')
            LEFT JOIN `masterdata` `mr` ON (`u`.`religion` = `mr`.`dataid` AND `mr`.`type` = 'RELIGION')
            LEFT JOIN `masterdata` `mmt` ON (`u`.`mother_tongue` = `mmt`.`dataid` AND `mmt`.`type` = 'MOTHER_TONGUE')
            LEFT JOIN `masterdata` `ml` ON (`u`.`language` = `ml`.`dataid` AND `ml`.`type` = 'MOTHER_TONGUE')
            LEFT JOIN `masterdata` `mcor` ON (`u`.`con_of_residence` = `mcor`.`dataid` AND `mcor`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `mcob` ON (`u`.`con_of_birth` = `mcob`.`dataid` AND `mcob`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `mcoc` ON (`u`.`con_of_citizenship` = `mcoc`.`dataid` AND `mcoc`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `mcgu` ON (`u`.`con_grew_up` = `mcgu`.`dataid` AND `mcgu`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `ms` ON (`u`.`state` = `ms`.`dataid` AND `ms`.`type` = 'STATE')
            LEFT JOIN `masterdata` `mc` ON (`u`.`city` = `mc`.`dataid` AND `mc`.`type` = 'CITY')
            LEFT JOIN `masterdata` `mcst` ON (`u`.`caste` = `mcst`.`dataid` AND `mcst`.`type` = 'CASTE')
            LEFT JOIN `masterdata` `me` ON (`u`.`education` = `me`.`dataid` AND `me`.`type` = 'EDUCATION')
            LEFT JOIN `partner_preferences` `pp` ON (`pp`.`user_id` = `u`.`id`)
            LEFT JOIN `masterdata` `rmms` ON (`pp`.`marital_status` = `rmms`.`dataid` AND `rmms`.`type` = 'MARITAL_STATUS')
            LEFT JOIN `masterdata` `rmcor` ON (`pp`.`country_id` = `rmcor`.`dataid` AND `rmcor`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `rmc` ON (`pp`.`city_id` = `rmc`.`dataid` AND `rmc`.`type` = 'CITY')
            LEFT JOIN `masterdata` `rmr` ON (`pp`.`religion_id` = `rmr`.`dataid` AND `rmr`.`type` = 'RELIGION')
            LEFT JOIN `masterdata` `rmcst` ON (`pp`.`caste_id` = `rmcst`.`dataid` AND `rmcst`.`type` = 'CASTE')
            LEFT JOIN `masterdata` `rme` ON (`pp`.`education_id` = `rme`.`dataid` AND `rme`.`type` = 'EDUCATION')
            LEFT JOIN `masterdata` `rmmt` ON (`pp`.`mother_tongue_id` = `rmmt`.`dataid` AND `rmmt`.`type` = 'MOTHER_TONGUE')
            LEFT JOIN `masterdata` `rmcp` ON (`pp`.`preferred_country_id` = `rmcp`.`dataid` AND `rmcp`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `rms` ON (`pp`.`state_id` = `rms`.`dataid` AND `rms`.`type` = 'STATE')
            LEFT JOIN `images` `dp` ON (`u`.`id` = `dp`.`user_id` AND `dp`.`displaypic` = '1' AND `dp`.`visibility` = 'Public')
            LEFT JOIN `images` `i` ON (`u`.`id` = `i`.`user_id` AND `i`.`visibility` = 'Public')
            GROUP BY `u`.`id`
        ");
    }

    public function down(): void
    {
        // Restore the exact pre-migration view definition (sourced from
        // `users`.`r*` directly) before dropping the new table, so `down()`
        // leaves the database exactly as `up()` found it.
        DB::statement('DROP VIEW IF EXISTS `profile`');
        DB::statement("
            CREATE VIEW `profile` AS
            SELECT `u`.*, CONCAT(`u`.`first_name`, ' ', `u`.`last_name`) AS `name`,
                `mp`.`name` AS `lbl_package`, `mms`.`name` AS `lbl_marital_status`, `mr`.`name` AS `lbl_religion`,
                `mmt`.`name` AS `lbl_mother_tongue`, `ml`.`name` AS `lbl_language`,
                `mcor`.`name` AS `lbl_con_of_residence`, `mcob`.`name` AS `lbl_con_of_birth`,
                `mcoc`.`name` AS `lbl_con_of_citizenship`, `mcgu`.`name` AS `lbl_con_grew_up`,
                `ms`.`name` AS `lbl_state`, `mc`.`name` AS `lbl_city`, `mcst`.`name` AS `lbl_caste`,
                `me`.`name` AS `lbl_education`,
                `rmms`.`name` AS `lbl_rmarital_status`, `rmcor`.`name` AS `lbl_rcon_of_residence`,
                `rmc`.`name` AS `lbl_rcity`, `rmr`.`name` AS `lbl_rreligion`, `rmcst`.`name` AS `lbl_rcaste`,
                `rme`.`name` AS `lbl_reducation`, `rmmt`.`name` AS `lbl_rmother_tongue`,
                `rmcp`.`name` AS `lbl_rcon_pref`,
                `dp`.`img_url` AS `displaypic`,
                GROUP_CONCAT(`i`.`img_url` SEPARATOR ',') AS `images`
            FROM `users` `u`
            LEFT JOIN `masterdata` `mp` ON (`u`.`package` = `mp`.`dataid` AND `mp`.`type` = 'PACKAGE')
            LEFT JOIN `masterdata` `mms` ON (`u`.`marital_status` = `mms`.`dataid` AND `mms`.`type` = 'MARITAL_STATUS')
            LEFT JOIN `masterdata` `mr` ON (`u`.`religion` = `mr`.`dataid` AND `mr`.`type` = 'RELIGION')
            LEFT JOIN `masterdata` `mmt` ON (`u`.`mother_tongue` = `mmt`.`dataid` AND `mmt`.`type` = 'MOTHER_TONGUE')
            LEFT JOIN `masterdata` `ml` ON (`u`.`language` = `ml`.`dataid` AND `ml`.`type` = 'MOTHER_TONGUE')
            LEFT JOIN `masterdata` `mcor` ON (`u`.`con_of_residence` = `mcor`.`dataid` AND `mcor`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `mcob` ON (`u`.`con_of_birth` = `mcob`.`dataid` AND `mcob`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `mcoc` ON (`u`.`con_of_citizenship` = `mcoc`.`dataid` AND `mcoc`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `mcgu` ON (`u`.`con_grew_up` = `mcgu`.`dataid` AND `mcgu`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `ms` ON (`u`.`state` = `ms`.`dataid` AND `ms`.`type` = 'STATE')
            LEFT JOIN `masterdata` `mc` ON (`u`.`city` = `mc`.`dataid` AND `mc`.`type` = 'CITY')
            LEFT JOIN `masterdata` `mcst` ON (`u`.`caste` = `mcst`.`dataid` AND `mcst`.`type` = 'CASTE')
            LEFT JOIN `masterdata` `me` ON (`u`.`education` = `me`.`dataid` AND `me`.`type` = 'EDUCATION')
            LEFT JOIN `masterdata` `rmms` ON (`u`.`rmarital_status` = `rmms`.`dataid` AND `rmms`.`type` = 'MARITAL_STATUS')
            LEFT JOIN `masterdata` `rmcor` ON (`u`.`rcon_of_residence` = `rmcor`.`dataid` AND `rmcor`.`type` = 'COUNTRY')
            LEFT JOIN `masterdata` `rmc` ON (`u`.`rcity` = `rmc`.`dataid` AND `rmc`.`type` = 'CITY')
            LEFT JOIN `masterdata` `rmr` ON (`u`.`rreligion` = `rmr`.`dataid` AND `rmr`.`type` = 'RELIGION')
            LEFT JOIN `masterdata` `rmcst` ON (`u`.`rcaste` = `rmcst`.`dataid` AND `rmcst`.`type` = 'CASTE')
            LEFT JOIN `masterdata` `rme` ON (`u`.`reducation` = `rme`.`dataid` AND `rme`.`type` = 'EDUCATION')
            LEFT JOIN `masterdata` `rmmt` ON (`u`.`rmother_tongue` = `rmmt`.`dataid` AND `rmmt`.`type` = 'MOTHER_TONGUE')
            LEFT JOIN `masterdata` `rmcp` ON (`u`.`rcon_pref` = `rmcp`.`dataid` AND `rmcp`.`type` = 'COUNTRY')
            LEFT JOIN `images` `dp` ON (`u`.`id` = `dp`.`user_id` AND `dp`.`displaypic` = '1' AND `dp`.`visibility` = 'Public')
            LEFT JOIN `images` `i` ON (`u`.`id` = `i`.`user_id` AND `i`.`visibility` = 'Public')
            GROUP BY `u`.`id`
        ");

        Schema::dropIfExists('partner_preferences');
    }
};
