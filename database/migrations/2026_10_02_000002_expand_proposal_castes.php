<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fuller proposal caste list (the client's list). Existing rows and their ids are kept, new names
 * are added, the list is shown alphabetically and "Other" stays last. Team members can still add
 * a caste that is not listed while adding a proposal.
 */
return new class extends Migration
{
    private array $castes = [
        'Arain', 'Awan', 'Baloch', 'Bhatti', 'Butt', 'Chaudhry', 'Dogar', 'Gujjar', 'Jat / Jutt', 'Kakazai', 'Kashmiri', 'Kamboh',
        'Khokhar', 'Malik', 'Mughal', 'Pathan / Pashtun', 'Qureshi', 'Rajput', 'Sheikh', 'Syed', 'Ansari', 'Abbasi', 'Hashmi',
        'Siddiqui', 'Farooqui', 'Usmani', 'Mirza', 'Memon', 'Khoja', 'Bohra', 'Lohana', 'Khatri', 'Arora', 'Saini', 'Gakhar',
        'Janjua', 'Minhas', 'Gondal', 'Tiwana', 'Kharral', 'Wattoo', 'Cheema', 'Chatha', 'Warraich', 'Bajwa', 'Sandhu', 'Gill',
        'Virk', 'Randhawa', 'Tarar', 'Dhillon', 'Sial', 'Sipra', 'Noon', 'Langrial', 'Khar', 'Leghari', 'Mazari', 'Bugti', 'Marri',
        'Rind', 'Jatoi', 'Khosa', 'Lund', 'Gopang', 'Chandio', 'Magsi', 'Jamali', 'Talpur', 'Soomro', 'Junejo', 'Bhutto', 'Mahar',
        'Panhwar', 'Kalhoro', 'Niazi', 'Khattak', 'Afridi', 'Yousafzai', 'Shinwari', 'Orakzai', 'Bangash', 'Wazir', 'Mehsud',
        'Tareen', 'Durrani', 'Kakar', 'Achakzai', 'Swati', 'Tanoli', 'Hazara', 'Rehmani', 'Hajjam', 'Mochi', 'Malik Tell',
        'Malik Kakazai', 'Kashmiri Butt', 'Kashmiri Mir', 'Kashmiri Wain', 'Mughal Lohar', 'Mughal Baig',
    ];

    public function up(): void
    {
        $now = now();
        $existing = DB::table('proposal_castes')->pluck('name')->map(fn ($n) => strtolower($n))->all();

        foreach ($this->castes as $name) {
            if (!in_array(strtolower($name), $existing, true)) {
                DB::table('proposal_castes')->insert(['name' => $name, 'sort_order' => 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
                $existing[] = strtolower($name);
            }
        }

        DB::table('proposal_castes')->update(['sort_order' => 0]);                       // alphabetical
        DB::table('proposal_castes')->where('name', 'Other')->update(['sort_order' => 9999]);   // Other last
    }

    public function down(): void
    {
        // Added castes may already be in use by proposals — left in place.
    }
};
