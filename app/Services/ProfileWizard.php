<?php

namespace App\Services;

use App\MasterData;
use App\PartnerPreference;
use App\User;
use Illuminate\Support\Facades\Log;

/**
 * "Complete your profile" wizard: which fields the member dashboard holds, grouped into steps, which of them are
 * required for a 100% profile, how far along a member is, and how a step is validated and saved. The same steps are used
 * to edit the profile later. Fields are the ones the dashboard's own edit forms save (users columns, plus the partner
 * preferences table), so nothing here introduces new data.
 */
class ProfileWizard
{
    /** Marital-status options that mean "never been married" (children is optional for these). */
    private const SIMPLE_SELECTS = [
        'gender' => ['male' => 'Male', 'female' => 'Female'],
        'profile_for' => ['Self' => 'Self', 'Son' => 'Son', 'Daughter' => 'Daughter', 'Brother' => 'Brother', 'Sister' => 'Sister', 'Relative' => 'Relative', 'Friend' => 'Friend'],
        'prayer' => ['Always' => 'Always', 'Sometimes' => 'Sometimes', 'Rarely' => 'Rarely', 'Never' => 'Never'],
        'smoking' => ['Yes' => 'Yes', 'No' => 'No', 'Occasionally' => 'Occasionally'],
        'diet' => ['Vegetarian' => 'Vegetarian', 'Non-Vegetarian' => 'Non-Vegetarian', 'Eggetarian' => 'Eggetarian', 'Vegan' => 'Vegan'],
        'exercise' => ['Regularly' => 'Regularly', 'Occasionally' => 'Occasionally', 'Never' => 'Never'],
        'living_arrangement' => ['With Family' => 'With Family', 'Alone' => 'Alone', 'With Roommates' => 'With Roommates', 'Other' => 'Other'],
        'family_residence' => ['Own' => 'Own', 'Rent' => 'Rent', 'Do not know' => 'Do not know'],
        'family_values' => ['Traditional' => 'Traditional', 'Moderate' => 'Moderate', 'Liberal' => 'Liberal'],
        'with_children' => ['Yes' => 'Yes', 'No' => 'No', 'Does not matter' => 'Does not matter'],
    ];

    /** @var array<string, array<string,string>> masterdata lists by type, loaded once per request */
    private array $masterCache = [];

    /** Height choices in the same "5' 4"" form registration saves. */
    public function heightOptions(): array
    {
        $out = [];
        for ($ft = 4; $ft <= 7; $ft++) {
            for ($in = 0; $in <= 11; $in++) {
                if ($ft === 4 && $in < 5) continue;
                if ($ft === 7 && $in > 0) break;
                $h = $ft . "' " . $in . '"';
                $out[$h] = $h;
            }
        }
        return $out;
    }

    public function master(string $type): array
    {
        if (!isset($this->masterCache[$type])) {
            $q = MasterData::where('type', $type);
            if ($type === 'COUNTRY') {
                $q->orderBy('order', 'DESC');
            }
            if (in_array($type, ['RELIGION', 'MOTHER_TONGUE'], true)) {
                $q->orderByRaw("name = 'Other' ASC");
            }
            $this->masterCache[$type] = $q->orderBy('name')->pluck('name', 'dataid')->all();
        }
        return $this->masterCache[$type];
    }

    /** A field: name, label, type (text|number|textarea|select|dob|country|state|city|caste), required, store (user|pref), column, options/source … */
    private function f(string $name, string $label, string $type, bool $required = false, array $extra = []): array
    {
        return array_merge(['name' => $name, 'label' => $label, 'type' => $type, 'required' => $required, 'store' => 'user', 'column' => $name], $extra);
    }

    /** @return array<int, array{key:string,title:string,desc:string,icon:string,fields:array}> */
    public function sections(): array
    {
        $f = fn (...$a) => $this->f(...$a);
        return [
            ['key' => 'basic', 'title' => 'Basic Information', 'icon' => 'fa-user', 'desc' => 'Who the profile is for and the basics families look at first.', 'fields' => [
                $f('first_name', 'First name', 'text', true, ['max' => 100]),
                $f('last_name', 'Last name', 'text', true, ['max' => 100]),
                $f('gender', 'Gender', 'select', true, ['source' => 'gender']),
                $f('birthday', 'Date of birth', 'dob', true),
                $f('marital_status', 'Marital status', 'select', true, ['master' => 'MARITAL_STATUS']),
                $f('children', 'Number of children', 'number', false, ['min' => 0, 'max' => 20, 'placeholder' => '0']),
                $f('profile_for', 'Profile created for', 'select', true, ['source' => 'profile_for']),
                $f('contact_mobile_number', 'Mobile number (with country code)', 'text', true, ['max' => 20, 'placeholder' => 'e.g. 923001234567']),
            ]],
            ['key' => 'education', 'title' => 'Education & Career', 'icon' => 'fa-graduation-cap', 'desc' => 'Your studies and work.', 'fields' => [
                $f('education', 'Highest education', 'select', true, ['master' => 'EDUCATION']),
                $f('profession', 'Profession / occupation', 'text', true, ['max' => 150]),
                $f('designation', 'Job title / designation', 'text', true, ['max' => 150, 'placeholder' => 'e.g. Senior Software Engineer']),
                $f('companyname', 'Company / business name', 'text', false, ['max' => 150]),
                $f('salary', 'Monthly income', 'text', true, ['max' => 100, 'placeholder' => 'e.g. PKR 150,000']),
            ]],
            ['key' => 'appearance', 'title' => 'Appearance', 'icon' => 'fa-male', 'desc' => 'Physical details.', 'fields' => [
                $f('height', 'Height', 'select', true, ['options' => $this->heightOptions()]),
                $f('weight', 'Weight', 'text', true, ['max' => 30, 'placeholder' => 'e.g. 65 kg']),
            ]],
            ['key' => 'religion', 'title' => 'Religion & Community', 'icon' => 'fa-moon-o', 'desc' => 'Faith and community background.', 'fields' => [
                $f('religion', 'Religion', 'select', true, ['master' => 'RELIGION']),
                $f('caste', 'Caste / community', 'caste', true, ['depends' => 'religion']),
                $f('sect', 'Sect', 'text', true, ['max' => 100]),
            ]],
            ['key' => 'location', 'title' => 'Location & Residency', 'icon' => 'fa-map-marker', 'desc' => 'Where you live and your roots.', 'fields' => [
                $f('con_of_residence', 'Country of residence', 'country', true),
                $f('state', 'State / province', 'state', false, ['depends' => 'con_of_residence', 'requiredIfOptions' => true]),
                $f('city', 'City', 'city', true, ['depends' => 'state']),
                $f('society', 'Area / society', 'text', false, ['max' => 150]),
                $f('con_of_birth', 'Country of birth', 'country', true),
                $f('con_of_citizenship', 'Citizenship', 'country', true),
                $f('con_grew_up', 'Country you grew up in', 'country', false),
                $f('immigration_status', 'Residency / immigration status', 'text', true, ['max' => 100, 'placeholder' => 'e.g. Citizen, Permanent resident, Work visa']),
            ]],
            ['key' => 'language', 'title' => 'Language & Lifestyle', 'icon' => 'fa-comments-o', 'desc' => 'Languages and everyday habits. Lifestyle answers are optional.', 'fields' => [
                $f('mother_tongue', 'Mother tongue', 'select', true, ['master' => 'MOTHER_TONGUE']),
                $f('language', 'Other language spoken', 'select', true, ['master' => 'MOTHER_TONGUE']),
                $f('prayer', 'Prayer', 'select', false, ['source' => 'prayer']),
                $f('smoking', 'Smoking', 'select', false, ['source' => 'smoking']),
                $f('diet', 'Diet', 'select', false, ['source' => 'diet']),
                $f('exercise', 'Exercise', 'select', false, ['source' => 'exercise']),
                $f('living_arrangement', 'Living arrangement', 'select', false, ['source' => 'living_arrangement']),
                $f('hobbies', 'Hobbies', 'text', false, ['max' => 200, 'placeholder' => 'e.g. Travel, Reading, Football']),
            ]],
            ['key' => 'family', 'title' => 'Family Background', 'icon' => 'fa-home', 'desc' => 'Your family helps us find suitable proposals.', 'fields' => [
                $f('father', "Father's name", 'text', true, ['max' => 100]),
                $f('mother', "Mother's name", 'text', true, ['max' => 100]),
                $f('father_profession', "Father's occupation", 'text', true, ['max' => 150]),
                $f('mother_profession', "Mother's occupation", 'text', false, ['max' => 150]),
                $f('brothers_count', 'Number of brothers', 'number', true, ['min' => 0, 'max' => 20]),
                $f('sisters_count', 'Number of sisters', 'number', true, ['min' => 0, 'max' => 20]),
                $f('siblings', 'About your siblings', 'text', false, ['max' => 200, 'placeholder' => 'e.g. 1 brother married']),
                $f('district', 'Family home district / town', 'text', true, ['max' => 100]),
                $f('family_residence', 'Family home', 'select', true, ['source' => 'family_residence']),
                $f('family_values', 'Family values', 'select', true, ['source' => 'family_values']),
                $f('special_circumstances', 'Anything else to share (optional)', 'text', false, ['max' => 200]),
            ]],
            ['key' => 'partner', 'title' => 'Partner Preferences', 'icon' => 'fa-heart', 'desc' => 'What you are looking for. Matching uses these.', 'fields' => [
                $f('age_min', 'Preferred age from', 'number', true, ['store' => 'pref', 'column' => 'age_min', 'min' => 18, 'max' => 99]),
                $f('age_max', 'Preferred age to', 'number', true, ['store' => 'pref', 'column' => 'age_max', 'min' => 18, 'max' => 99]),
                $f('pref_marital_status', 'Preferred marital status', 'select', true, ['store' => 'pref', 'column' => 'marital_status', 'master' => 'MARITAL_STATUS']),
                $f('pref_with_children', 'Would you accept a partner with children?', 'select', true, ['store' => 'pref', 'column' => 'with_children', 'source' => 'with_children']),
                $f('pref_religion', 'Preferred religion', 'select', true, ['store' => 'pref', 'column' => 'religion_id', 'master' => 'RELIGION']),
                $f('pref_country', 'Preferred country of residence', 'select', true, ['store' => 'pref', 'column' => 'country_id', 'master' => 'COUNTRY']),
                $f('pref_education', 'Minimum education', 'select', true, ['store' => 'pref', 'column' => 'education_id', 'master' => 'EDUCATION']),
                $f('pref_note', 'Anything else you want in a partner (optional)', 'textarea', false, ['store' => 'pref', 'column' => 'general_requirement', 'max' => 1000]),
            ]],
            ['key' => 'about', 'title' => 'About Me', 'icon' => 'fa-pencil', 'desc' => 'A short, respectful introduction.', 'fields' => [
                $f('intro', 'Introduction', 'textarea', true, ['column' => 'intro', 'max' => 2000, 'minlen' => 30, 'placeholder' => 'Tell families about yourself, your interests and what matters to you (at least 30 characters).']),
            ]],
        ];
    }

    /** Options of a select-like field as [value => label]. */
    public function optionsOf(array $field): array
    {
        if (!empty($field['options'])) return $field['options'];
        if (!empty($field['master'])) return $this->master($field['master']);
        if (!empty($field['source'])) return self::SIMPLE_SELECTS[$field['source']] ?? [];
        if ($field['type'] === 'country') return $this->master('COUNTRY');
        return [];
    }

    /** The member's current value for a field. */
    public function valueOf(User $user, array $field, ?PartnerPreference $pref)
    {
        if ($field['store'] === 'pref') {
            return $pref ? $pref->{$field['column']} : null;
        }
        $v = $user->{$field['column']};
        if ($field['type'] === 'dob') {
            return $v ? substr((string) $v, 0, 10) : null;
        }
        return $v;
    }

    private function filled($v): bool
    {
        return $v !== null && trim((string) $v) !== '';
    }

    /** Is a field required for this member right now (a state is only needed when the country has states). */
    private function isRequired(array $field, User $user): bool
    {
        if (!empty($field['requiredIfOptions'])) {
            return !empty($user->con_of_residence) && MasterData::where(['type' => 'STATE', 'subtype' => $user->con_of_residence])->exists();
        }
        return $field['required'];
    }

    /**
     * @return array{percent:int,filled:int,total:int,complete:bool,sections:array<string,array{filled:int,total:int,done:bool}>,missing:array<int,array{section:string,title:string,label:string,name:string}>}
     */
    public function status(User $user): array
    {
        $pref = $user->partnerPreference()->first();
        $filled = 0;
        $total = 0;
        $sections = [];
        $missing = [];
        foreach ($this->sections() as $section) {
            $sf = 0;
            $st = 0;
            foreach ($section['fields'] as $field) {
                if (!$this->isRequired($field, $user)) continue;
                $st++;
                $v = $this->valueOf($user, $field, $pref);
                $ok = $this->filled($v) && (empty($field['minlen']) || mb_strlen(trim((string) $v)) >= $field['minlen']);
                if ($ok) {
                    $sf++;
                } else {
                    $missing[] = ['section' => $section['key'], 'title' => $section['title'], 'label' => $field['label'], 'name' => $field['name']];
                }
            }
            $sections[$section['key']] = ['filled' => $sf, 'total' => $st, 'done' => $sf >= $st];
            $filled += $sf;
            $total += $st;
        }
        $percent = $total > 0 ? (int) floor($filled / $total * 100) : 100;

        return ['percent' => $percent, 'filled' => $filled, 'total' => $total, 'complete' => $filled >= $total, 'sections' => $sections, 'missing' => $missing];
    }

    public function complete(User $user): bool
    {
        return $this->status($user)['complete'];
    }

    /**
     * Validates and saves one step. Draft-friendly: a field left empty is simply not saved, but whatever IS sent must be valid.
     *
     * @return array{errors:array<string,string>}
     */
    public function saveStep(User $user, string $sectionKey, array $input, bool $draft = false): array
    {
        $section = collect($this->sections())->firstWhere('key', $sectionKey);
        if (!$section) {
            return ['errors' => ['_' => 'Unknown section.']];
        }

        $errors = [];
        $userChanges = [];
        $prefChanges = [];
        $country = $input['con_of_residence'] ?? $user->con_of_residence;

        foreach ($section['fields'] as $field) {
            $name = $field['name'];
            if (!array_key_exists($name, $input) && $field['type'] !== 'dob') continue;
            $raw = $field['type'] === 'dob' ? null : $input[$name];
            $value = is_string($raw) ? trim($raw) : $raw;

            if ($field['type'] === 'dob') {
                $d = (int) ($input['day'] ?? 0); $m = (int) ($input['month'] ?? 0); $y = (int) ($input['year'] ?? 0);
                if (!$d && !$m && !$y) continue;
                if (!checkdate($m, $d, $y)) { $errors[$name] = 'Please choose a real date of birth.'; continue; }
                $dob = sprintf('%04d-%02d-%02d', $y, $m, $d);
                if ($dob > date('Y-m-d', strtotime('-18 years'))) { $errors[$name] = 'You must be at least 18 years old.'; continue; }
                $value = $dob;
            }

            if ($value === '' || $value === null) {
                if ($draft) {
                    continue;   // a draft keeps whatever is already saved and ignores empty boxes
                }
                if ($field['required']) {
                    $errors[$name] = $field['label'] . ' is required.';
                } elseif (!empty($field['requiredIfOptions']) && $country && MasterData::where(['type' => 'STATE', 'subtype' => $country])->exists()) {
                    $errors[$name] = $field['label'] . ' is required.';
                }
                $this->assign($field, null, $userChanges, $prefChanges, true);
                continue;
            }

            $error = $this->validateValue($field, $value, $input, $country);
            if ($error) { $errors[$name] = $error; continue; }
            $this->assign($field, $value, $userChanges, $prefChanges);
        }

        if ($sectionKey === 'partner' && isset($prefChanges['age_min'], $prefChanges['age_max']) && $prefChanges['age_min'] > $prefChanges['age_max']) {
            $errors['age_max'] = '"Age to" must not be lower than "Age from".';
        }

        if ($errors) {
            return ['errors' => $errors];
        }

        if ($userChanges) {
            $user->forceFill($userChanges)->save();
        }
        if ($prefChanges) {
            PartnerPreference::updateOrCreate(['user_id' => $user->id], $prefChanges);
        }
        Log::info('User (' . $user->dataid . ') saved profile step ' . $sectionKey . ' in the profile wizard.');
        User::retrieveUserObject($user->dataid, true);

        return ['errors' => []];
    }

    private function assign(array $field, $value, array &$userChanges, array &$prefChanges, bool $clearing = false): void
    {
        if ($clearing && $field['required']) return;   // never blank out a saved required value from a draft save
        if ($field['store'] === 'pref') {
            $prefChanges[$field['column']] = $value === null ? null : $value;
        } else {
            $userChanges[$field['column']] = $value === null ? null : $value;
        }
    }

    private function validateValue(array $field, $value, array $input, $country): ?string
    {
        $label = $field['label'];
        $str = (string) $value;
        switch ($field['type']) {
            case 'text':
            case 'textarea':
                if (mb_strlen($str) > ($field['max'] ?? 255)) return $label . ' is too long.';
                if (!empty($field['minlen']) && mb_strlen($str) < $field['minlen']) return $label . ' must be at least ' . $field['minlen'] . ' characters.';
                if ($field['name'] === 'contact_mobile_number' && !preg_match('/^\+?\d{8,15}$/', $str)) return 'Enter a valid mobile number with country code (digits only).';
                return null;
            case 'number':
                if (!ctype_digit($str)) return $label . ' must be a whole number.';
                if ((int) $str < ($field['min'] ?? 0) || (int) $str > ($field['max'] ?? 99)) return $label . ' must be between ' . ($field['min'] ?? 0) . ' and ' . ($field['max'] ?? 99) . '.';
                return null;
            case 'select':
            case 'country':
                return array_key_exists($str, $this->optionsOf($field)) ? null : 'Please choose a valid ' . strtolower($label) . '.';
            case 'caste':
                return MasterData::where(['type' => 'CASTE', 'dataid' => $str])->exists() ? null : 'Please choose a valid caste.';
            case 'state':
                return MasterData::where(['type' => 'STATE', 'dataid' => $str])->exists() ? null : 'Please choose a valid state.';
            case 'city':
                return MasterData::where(['type' => 'CITY', 'dataid' => $str])->exists() ? null : 'Please choose a valid city.';
        }
        return null;
    }
}
