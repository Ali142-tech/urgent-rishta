<?php

namespace App\Services;

use App\MasterData;
use App\Proposal;
use App\ProposalCaste;
use App\ProposalPhoto;
use App\TeamMember;
use App\User;
use Illuminate\Support\Facades\File;

/**
 * Copies a site member (users row + partner preferences + photos) into a team Proposal, in the shape the
 * Add / Edit Proposal form uses. The member stays as they are; the proposal is a separate copy owned by the
 * given team account. Values that the proposal pick-lists do not have are carried over as plain text (the
 * form's "Other" path) and anything that cannot be matched at all is left empty for the team to fill in.
 */
class MemberToProposal
{
    /** The proposal already made from this member, if any. */
    public function existing(User $user): ?Proposal
    {
        return Proposal::withoutGlobalScopes()->where('source_user_id', $user->id)->first();
    }

    public function convert(User $user, TeamMember $owner): Proposal
    {
        $pref = $user->partnerPreference;
        $name = fn ($type, $id) => $id ? (string) MasterData::where('type', $type)->where('dataid', (string) $id)->value('name') : '';

        $country = $this->countryName($name('COUNTRY', $user->con_of_residence));
        $nationality = $this->nationalityFor($this->countryName($name('COUNTRY', $user->con_of_citizenship)));
        $caste = $this->proposalCaste($name('CASTE', $user->caste));
        $education = $name('EDUCATION', $user->education);
        $religion = $name('RELIGION', $user->religion);
        $income = trim((string) ($user->salary ?? ''));
        $currentCity = $name('CITY', $user->city);
        $birthday = $user->birthday ? substr((string) $user->birthday, 0, 10) : null;

        $siblings = [];
        if ($user->brothers_count !== null && $user->brothers_count !== '') $siblings[] = $user->brothers_count . ' Brother' . ((int) $user->brothers_count === 1 ? '' : 's');
        if ($user->sisters_count !== null && $user->sisters_count !== '') $siblings[] = $user->sisters_count . ' Sister' . ((int) $user->sisters_count === 1 ? '' : 's');

        $father = $this->join(' / ', [$user->father, $user->father_profession]);
        $mother = $this->join(' / ', [$user->mother, $user->mother_profession]);
        $familyValues = (string) $user->family_values;

        // partner side
        $prefEducation = $pref ? $name('EDUCATION', $pref->education_id) : '';
        $prefCaste = $pref && $pref->caste_id ? $this->proposalCaste($name('CASTE', $pref->caste_id)) : null;
        $prefMarital = $pref && $pref->marital_status ? array_values(array_filter(array_map('trim', explode(',', (string) $pref->marital_status)))) : [];
        $prefCity = '';
        if ($pref) {
            $prefCity = trim((string) $pref->pref_city) ?: $name('CITY', $pref->city_id) ?: $this->countryName($name('COUNTRY', $pref->country_id));
        }
        $prefLanguages = $pref && $pref->languages
            ? implode(', ', array_filter(array_map(fn ($id) => $name('LANGUAGE', trim($id)) ?: $name('MOTHER_TONGUE', trim($id)), explode(',', (string) $pref->languages))))
            : '';
        $prefNote = $pref ? $this->join("\n", [$pref->general_requirement, $pref->pref_caste_note, $pref->pref_qualification_note]) : '';

        $proposal = Proposal::create([
            'added_by' => $owner->id,
            'is_private' => false,
            'source_user_id' => $user->id,
            'is_draft' => true,   // hidden everywhere until the admin saves it on the edit page
            'gender' => in_array($user->gender, ['male', 'female'], true) ? $user->gender : 'male',
            'birthday' => $birthday,
            'height' => $this->height($user->height),
            'marital_status' => $user->marital_status ?: null,
            'education' => $education ?: null,
            'profession' => $user->profession ?: null,
            'caste_id' => $caste ? $caste->id : null,
            'sect' => $user->sect ?: null,
            'con_of_residence' => $country ?: null,
            'con_of_citizenship' => $nationality ?: null,
            'current_city' => $currentCity ?: null,
            'city' => $user->hometown ?: null,
            'family_status' => in_array($user->family_status, ['Middle Class', 'Upper Middle Class', 'Royal Class'], true) ? $user->family_status : null,
            'raw_intake_text' => $this->intakeText($user, compact('country', 'nationality', 'education', 'religion', 'income', 'currentCity', 'siblings', 'father', 'mother', 'prefEducation', 'prefCity', 'prefLanguages', 'prefNote')) ,
            'religion' => $religion ?: null,
            'monthly_income' => $income !== '' ? mb_substr($income, 0, 100) : null,
            'residential_area' => $user->society ? mb_substr($user->society, 0, 200) : null,
            'house_size' => $this->join(' / ', [$user->residence_size, $user->property_ownership]) ?: null,
            'father_details' => $father ? mb_substr($father, 0, 200) : null,
            'mother_details' => $mother ? mb_substr($mother, 0, 200) : null,
            'siblings' => $siblings ? implode(', ', $siblings) : null,
            'married_siblings' => $user->siblings_married_note ? mb_substr($user->siblings_married_note, 0, 100) : null,
            'family_values' => $familyValues !== '' ? mb_substr($familyValues, 0, 60) : null,
            'pref_age_min' => $pref && $pref->age_min ? (int) $pref->age_min : null,
            'pref_age_max' => $pref && $pref->age_max ? (int) $pref->age_max : null,
            'pref_city' => $prefCity ?: null,
            'pref_professions' => $pref && $pref->profession ? array_values(array_filter(array_map('trim', explode(',', $pref->profession)))) : null,
            'pref_profession' => $pref && $pref->profession ? mb_substr($pref->profession, 0, 150) : null,
            'pref_castes' => $prefCaste ? [(string) $prefCaste->id] : null,
            'pref_educations' => $prefEducation ? [$prefEducation] : null,
            'pref_marital_statuses' => $prefMarital ?: null,
            'pref_languages' => $prefLanguages ? mb_substr($prefLanguages, 0, 200) : null,
            'pref_note' => $prefNote ?: null,
        ]);

        $proposal = $proposal->fresh();
        $this->copyPhotos($user, $proposal);

        return $proposal;
    }

    private function join(string $glue, array $parts): string
    {
        return implode($glue, array_filter(array_map(fn ($p) => trim((string) $p), $parts), fn ($p) => $p !== ''));
    }

    /** "5' 0\"" → "5'0\"" (the form's height options), or null when it is not a usual height. */
    private function height($value): ?string
    {
        $h = str_replace(' ', '', (string) $value);
        return preg_match('/^[4-7]\'\d{1,2}"$/', $h) ? $h : null;
    }

    /** The form's country name for a masterdata country ("United Kingdom" → "United Kingdom (UK)"); else the name as it is. */
    private function countryName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '';
        }
        $list = array_keys(config('proposal_options.countries'));
        foreach ($list as $c) {
            if (strcasecmp($c, $name) === 0) return $c;
        }
        foreach ($list as $c) {
            if (stripos($c, $name . ' (') === 0 || stripos($c, $name) === 0) return $c;
        }
        return $name;
    }

    private function nationalityFor(string $country): string
    {
        $i = array_search($country, array_keys(config('proposal_options.countries')), true);
        return $i === false ? '' : (config('proposal_options.nationalities')[$i] ?? '');
    }

    /** The proposal caste with the member's caste name; none when the short proposal list does not have it. */
    private function proposalCaste(string $name): ?ProposalCaste
    {
        return $name === '' ? null : ProposalCaste::whereRaw('lower(name) = ?', [mb_strtolower(trim($name))])->first();
    }

    /** The member's details written the way the client intake form is, so "Original pasted form" on the proposal is not empty. */
    private function intakeText(User $user, array $v): string
    {
        $lines = [
            '01 | PERSONAL INFORMATION',
            'Full Name: ' . trim($user->first_name . ' ' . $user->last_name),
            'Gender: ' . ucfirst((string) $user->gender),
            'Age / Date of Birth: ' . ($user->birthday ? substr((string) $user->birthday, 0, 10) : ''),
            'Height: ' . $user->height,
            'Religion: ' . $v['religion'],
            'Sect: ' . $user->sect,
            'Education: ' . $v['education'],
            'Profession / Designation: ' . $this->join(' / ', [$user->profession, $user->designation]),
            'Monthly Income: ' . $v['income'],
            '',
            '02 | LOCATION & RESIDENCE',
            'Country: ' . $v['country'],
            'City: ' . $v['currentCity'],
            'Nationality / Visa Status: ' . $this->join(' / ', [$v['nationality'], $user->immigration_status]),
            'Hometown: ' . $user->hometown,
            'Residential Area / Society: ' . $user->society,
            'House Size / Ownership: ' . $this->join(' / ', [$user->residence_size, $user->property_ownership]),
            '',
            '03 | FAMILY BACKGROUND',
            "Father's Name / Profession: " . $v['father'],
            "Mother's Name / Profession: " . $v['mother'],
            'Brothers / Sisters: ' . implode(', ', $v['siblings']),
            'Family Values: ' . $user->family_values,
            '',
            '04 | PARTNER PREFERENCES',
            'Education: ' . $v['prefEducation'],
            'Preferred City / Country: ' . $v['prefCity'],
            'Languages / Interests: ' . $v['prefLanguages'],
            'Additional Requirements: ' . $v['prefNote'],
        ];
        $text = implode("\n", $lines);
        // the form needs at least 100 characters to save an edit
        return mb_substr($text, 0, 5000);
    }

    /** The member's photos, copied into the proposal photo folder (with their thumbnails). */
    private function copyPhotos(User $user, Proposal $proposal): void
    {
        $profile = $user->profile(true);
        $images = $profile->images ?? [];
        if (!is_array($images)) {
            $images = $images ? explode(',', $images) : [];
        }

        $dir = public_path(trim(Proposal::PHOTO_PATH, '/'));
        File::ensureDirectoryExists($dir);
        $order = 0;
        foreach ($images as $image) {
            $source = public_path(trim((string) $image));
            $file = basename((string) $image);
            if ($file === '' || !is_file($source)) {
                continue;
            }
            $order++;
            $newName = time() . '_' . $proposal->id . '_' . random_int(1000, 9999) . '.' . pathinfo($file, PATHINFO_EXTENSION);
            File::copy($source, $dir . '/' . $newName);
            foreach (['thumbnail_', 'thumbnail_sm_'] as $prefix) {
                $thumb = dirname($source) . '/' . $prefix . $file;
                File::copy(is_file($thumb) ? $thumb : $source, $dir . '/' . $prefix . $newName);
            }
            ProposalPhoto::create(['proposal_id' => $proposal->id, 'file_name' => $newName, 'is_main' => $order === 1, 'sort_order' => $order]);
        }
    }
}
