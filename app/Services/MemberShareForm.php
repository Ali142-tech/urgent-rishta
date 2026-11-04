<?php

namespace App\Services;

use App\MasterData;
use App\User;

/**
 * A member's complete profile written as the client form (the same sections the team's intake template uses),
 * ready to be sent to that member on WhatsApp. Contact details (email, phone, address) are never part of it.
 */
class MemberShareForm
{
    public function text(User $user): string
    {
        $pref = $user->partnerPreference;
        $name = fn ($type, $id) => $id ? (string) MasterData::where('type', $type)->where('dataid', (string) $id)->value('name') : '';
        $join = fn (string $glue, array $parts) => implode($glue, array_filter(array_map(fn ($p) => trim((string) $p), $parts), fn ($p) => $p !== ''));

        $siblings = [];
        if ($user->brothers_count !== null && $user->brothers_count !== '') $siblings[] = $user->brothers_count . ' Brother' . ((int) $user->brothers_count === 1 ? '' : 's');
        if ($user->sisters_count !== null && $user->sisters_count !== '') $siblings[] = $user->sisters_count . ' Sister' . ((int) $user->sisters_count === 1 ? '' : 's');

        $prefLanguages = $pref && $pref->languages
            ? implode(', ', array_filter(array_map(fn ($id) => $name('LANGUAGE', trim($id)) ?: $name('MOTHER_TONGUE', trim($id)), explode(',', (string) $pref->languages))))
            : '';
        $prefAge = $pref && ($pref->age_min || $pref->age_max) ? $join(' - ', [$pref->age_min, $pref->age_max]) : '';

        $sections = [
            '01 | PERSONAL INFORMATION' => [
                'Full Name' => trim($user->first_name . ' ' . $user->last_name),
                'Gender' => ucfirst((string) $user->gender),
                'Age / Date of Birth' => $user->birthday ? substr((string) $user->birthday, 0, 10) : '',
                'Height' => $user->height,
                'Marital Status' => $name('MARITAL_STATUS', $user->marital_status),
                'Religion' => $name('RELIGION', $user->religion),
                'Sect' => $user->sect,
                'Caste' => $name('CASTE', $user->caste),
                'Mother Tongue' => $name('MOTHER_TONGUE', $user->mother_tongue),
                'Education' => $name('EDUCATION', $user->education),
                'Profession / Designation' => $join(' / ', [$user->profession, $user->designation]),
                'Monthly Income' => $user->salary,
            ],
            '02 | LOCATION & RESIDENCE' => [
                'Country' => $name('COUNTRY', $user->con_of_residence),
                'City' => $name('CITY', $user->city),
                'Nationality / Visa Status' => $join(' / ', [$name('COUNTRY', $user->con_of_citizenship), $user->immigration_status]),
                'Hometown' => $user->hometown,
                'Residential Area / Society' => $user->society,
                'House Size / Ownership' => $join(' / ', [$user->residence_size, $user->property_ownership]),
            ],
            '03 | FAMILY BACKGROUND' => [
                "Father's Name / Profession" => $join(' / ', [$user->father, $user->father_profession]),
                "Mother's Name / Profession" => $join(' / ', [$user->mother, $user->mother_profession]),
                'Brothers / Sisters' => implode(', ', $siblings),
                'Family Values' => $user->family_values,
                'Family Status' => $user->family_status,
            ],
            '04 | PARTNER PREFERENCES' => [
                'Preferred Age Range' => $prefAge,
                'Marital Status' => $pref ? $name('MARITAL_STATUS', $pref->marital_status) : '',
                'Education' => $pref ? $name('EDUCATION', $pref->education_id) : '',
                'Profession' => $pref ? $pref->profession : '',
                'Preferred City / Country' => $pref ? ($pref->pref_city ?: $name('CITY', $pref->city_id) ?: $name('COUNTRY', $pref->country_id)) : '',
                'Languages' => $prefLanguages,
                'Additional Requirements' => $pref ? $join("\n", [$pref->general_requirement, $pref->pref_caste_note, $pref->pref_qualification_note]) : '',
            ],
        ];

        $lines = ['*YOUR PROFILE*', ''];
        foreach ($sections as $title => $fields) {
            $lines[] = '*' . $title . '*';
            foreach ($fields as $label => $value) {
                $lines[] = $label . ': ' . trim((string) $value);
            }
            $lines[] = '';
        }
        return rtrim(implode("\n", $lines));
    }

    /** The closing note (approved by the client), addressed to the member by first name. */
    public function closing(User $user): string
    {
        $first = trim((string) $user->first_name);

        return ($first !== '' ? 'Dear ' . $first . ', w' : 'W') . 'e have reviewed your profile. For other services or advanced matchmaking services, please contact our team at 0304-0227000.';
    }

    /** Full URLs of the member's own photos (up to $limit). */
    public function photoUrls(User $user, int $limit = 6): array
    {
        $images = $user->profile(true)->images ?? [];
        if (!is_array($images)) {
            $images = $images ? explode(',', $images) : [];
        }

        $urls = [];
        foreach ($images as $image) {
            $path = trim((string) $image);
            if ($path !== '' && is_file(public_path($path))) {
                $urls[] = url($path);
            }
            if (count($urls) >= $limit) break;
        }
        return $urls;
    }
}
