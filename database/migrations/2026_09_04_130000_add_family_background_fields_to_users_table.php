<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Family Background gap-fill (client field list). The existing free-text
     * father/mother/brother/sister columns are left exactly as they are —
     * these are purely additive, new fields alongside them. `siblings`
     * already exists (legacy, no edit form anywhere) and is repurposed here
     * for "Married Siblings" rather than adding yet another column for it.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mother_profession', 100)->nullable()->after('father_profession');
            $table->unsignedTinyInteger('brothers_count')->nullable()->after('brother');
            $table->unsignedTinyInteger('sisters_count')->nullable()->after('sister');
            $table->string('family_values', 20)->nullable()->after('family_residence');
        });

        // The `profile` VIEW (recreated by create_partner_preferences_table with an
        // explicit column list, not `u.*`) needs these 4 added too — anything that
        // queries it directly via Eloquent (AdminController::downloadProfilePdf uses
        // Profile::where(...)->first()) won't see new `users` columns otherwise, even
        // though the raw-SQL Profile::profiles() helper (which uses `u.*` and is what
        // powers the My Profile page) picks them up automatically.
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
                `u`.`brother` AS `brother`, `u`.`brothers_count` AS `brothers_count`,
                `u`.`sister` AS `sister`, `u`.`sisters_count` AS `sisters_count`,
                `u`.`district` AS `district`,
                `u`.`family_residence` AS `family_residence`, `u`.`family_values` AS `family_values`,
                `u`.`father_profession` AS `father_profession`, `u`.`mother_profession` AS `mother_profession`,
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mother_profession', 'brothers_count', 'sisters_count', 'family_values']);
        });

        // Restore the view to exactly what create_partner_preferences_table left it
        // as (without the 4 columns this migration added).
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
};
