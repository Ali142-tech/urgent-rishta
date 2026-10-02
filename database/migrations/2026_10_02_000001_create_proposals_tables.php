<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Proposals get their own tables instead of living in `users` as login-less
 * "shell" rows (users.added_by IS NOT NULL). A proposal is a lean record
 * curated by a matchmaker — no password, email, package, verification, blurred
 * photos... — with its own readable reference (P-001, P-002 ...) and its own
 * short caste list.
 *
 * Old proposals in `users` are deliberately NOT copied (the client agreed they
 * can be dropped); a later cleanup removes them. successful_matches rows that
 * pointed at them are cleared and the foreign keys now point at `proposals`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_castes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // The short list the client asked for (the site-wide caste list has ~420 entries).
        // Editable later from the admin panel / DB; "Other" stays last.
        $castes = ['Arain', 'Rajput', 'Jutt', 'Mughal', 'Malik', 'Syed', 'Awan', 'Sheikh', 'Gujjar', 'Pathan', 'Qureshi', 'Ansari', 'Butt', 'Other'];
        $now = now();
        foreach ($castes as $i => $name) {
            DB::table('proposal_castes')->insert([
                'name' => $name, 'sort_order' => $i + 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            // Readable reference shown everywhere (cards, shares, WhatsApp): P-001, P-002 ...
            // Filled right after insert from the row id, so it is unique and never reused.
            $table->string('reference', 20)->nullable()->unique();
            $table->unsignedBigInteger('added_by')->index();          // team_members.id of the matchmaker who owns it

            $table->string('gender', 10)->index();
            $table->date('birthday')->nullable();
            $table->string('height', 20)->nullable();
            $table->string('marital_status', 20)->nullable();          // masterdata.dataid
            $table->string('education', 20)->nullable();               // masterdata.dataid
            $table->string('profession', 150)->nullable();
            $table->unsignedBigInteger('caste_id')->nullable();        // proposal_castes.id
            $table->string('sect', 100)->nullable();
            $table->string('con_of_residence', 20)->nullable();        // masterdata.dataid (country)
            $table->string('con_of_citizenship', 20)->nullable();      // masterdata.dataid (nationality)
            $table->string('current_city', 150)->nullable();
            $table->string('city', 150)->nullable();                   // hometown

            // Classification the team picks by hand
            $table->string('family_status', 30)->nullable();
            $table->string('looking_from', 20)->nullable();
            $table->string('presentation_highlight', 30)->nullable();

            $table->string('profile_status', 20)->default('active')->index();
            $table->boolean('active')->default(true);
            $table->text('raw_intake_text')->nullable();               // the original pasted form, verbatim

            // Partner requirements (what this client is looking for)
            $table->unsignedTinyInteger('pref_age_min')->nullable();
            $table->unsignedTinyInteger('pref_age_max')->nullable();
            $table->string('pref_height', 100)->nullable();
            $table->string('pref_city', 150)->nullable();
            $table->string('pref_profession', 150)->nullable();
            $table->text('pref_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('caste_id')->references('id')->on('proposal_castes')->nullOnDelete();
        });

        Schema::create('proposal_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proposal_id')->index();
            $table->string('file_name', 190);
            $table->boolean('is_main')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('proposal_id')->references('id')->on('proposals')->onDelete('cascade');
        });

        // successful_matches pointed proposal_id / counterpart_proposal_id at users.id.
        DB::table('successful_matches')->delete();
        Schema::table('successful_matches', function (Blueprint $table) {
            $table->dropForeign(['proposal_id']);
            $table->dropForeign(['counterpart_proposal_id']);
            $table->dropForeign(['partner_a_id']);
            $table->dropForeign(['partner_b_id']);
        });
        Schema::table('successful_matches', function (Blueprint $table) {
            $table->foreign('proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            $table->foreign('counterpart_proposal_id')->references('id')->on('proposals')->onDelete('cascade');
            // The two matchmakers are team members now, not users.
            $table->foreign('partner_a_id')->references('id')->on('team_members')->onDelete('cascade');
            $table->foreign('partner_b_id')->references('id')->on('team_members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('successful_matches', function (Blueprint $table) {
            $table->dropForeign(['proposal_id']);
            $table->dropForeign(['counterpart_proposal_id']);
            $table->dropForeign(['partner_a_id']);
            $table->dropForeign(['partner_b_id']);
        });
        DB::table('successful_matches')->delete();
        Schema::table('successful_matches', function (Blueprint $table) {
            $table->foreign('proposal_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('counterpart_proposal_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('partner_a_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('partner_b_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::dropIfExists('proposal_photos');
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('proposal_castes');
    }
};
