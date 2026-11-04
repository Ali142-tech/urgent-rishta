<?php

namespace App\Http\Controllers;

use App\MasterData;
use App\Services\ProfileWizard;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * The "Complete your profile" wizard: a step-by-step page with the fields the member dashboard holds. New accounts are held
 * here (see EnsureProfileComplete) until the profile is 100%; everyone can come back later to edit the same details.
 */
class ProfileCompletionController extends Controller
{
    public function __construct(private ProfileWizard $wizard)
    {
        $this->middleware(['auth', 'verified']);
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $pref = $user->partnerPreference()->first();
        $status = $this->wizard->status($user);

        $sections = [];
        $values = [];
        foreach ($this->wizard->sections() as $section) {
            foreach ($section['fields'] as &$field) {
                $values[$field['name']] = $this->wizard->valueOf($user, $field, $pref);
                if (in_array($field['type'], ['select', 'country', 'profession'], true)) {
                    $field['choices'] = $this->wizard->optionsOf($field);
                    // an older answer that is not in the list (e.g. an income typed before) is kept as the member's current choice
                    if (!empty($field['keepCurrent']) && !empty($values[$field['name']]) && !array_key_exists($values[$field['name']], $field['choices'])) {
                        $field['choices'] = [$values[$field['name']] => $values[$field['name']] . ' (current)'] + $field['choices'];
                    }
                }
            }
            unset($field);
            $sections[] = $section;
        }

        // an older / typed qualification is shown as "Other (Please Specify)" with the member's own words
        $educationOther = '';
        if (!empty($values['education']) && !\App\Services\EducationOptions::inList($values['education'])) {
            $educationOther = (string) MasterData::where(['type' => 'EDUCATION', 'dataid' => $values['education']])->value('name');
            $values['education'] = \App\Services\EducationOptions::otherId();
        }

        // a profession that is not in the list (typed earlier, or from before) shows as "Other (Please Specify)" with the member's words
        $professionOther = [];
        foreach (['profession', 'father_profession', 'mother_profession'] as $pf) {
            if (!empty($values[$pf]) && !\App\Services\ProfessionOptions::inList($values[$pf])) {
                $professionOther[$pf] = (string) $values[$pf];
                $values[$pf] = \App\Services\ProfessionOptions::OTHER_VALUE;
            }
        }

        // dependent lists for what is already saved (they reload by themselves as the member changes the parent)
        $dep = [
            'castes' => $user->religion ? \App\Services\CommunityOptions::for($user->con_of_residence, $user->religion)->pluck('name', 'dataid')->all() : [],
            'states' => $user->con_of_residence ? $this->stateOptions($user->con_of_residence) : [],
            'cities' => $this->cityOptions($user->state, $user->con_of_residence),
        ];

        $keys = array_column($sections, 'key');
        $start = $request->query('step');
        if (!in_array($start, $keys, true)) {
            $start = collect($status['sections'])->filter(fn ($s) => !$s['done'])->keys()->first() ?? $keys[0];
        }

        $dob = !empty($values['birthday']) ? explode('-', $values['birthday']) : [null, null, null];

        // a new member still being gated sees the full-page wizard; once the profile is complete, "Edit Full Profile" opens the same wizard inside the dashboard
        return view(!empty($user->must_complete_profile) ? 'member.complete-profile' : 'member.edit-profile', [
            'sections' => $sections, 'values' => $values, 'dep' => $dep, 'status' => $status, 'start' => $start,
            'dob' => ['year' => $dob[0] ?? null, 'month' => $dob[1] ?? null, 'day' => $dob[2] ?? null],
            'gated' => !empty($user->must_complete_profile),
            'user' => $user,
            'avatar' => $this->avatarOf($user),
            'educationOther' => $educationOther,
            'houseSizes' => $this->wizard->houseSizeSets(),
            'professionOther' => $professionOther,
            'professionOtherLabel' => \App\Services\ProfessionOptions::otherLabel(),
            'educationOtherId' => \App\Services\EducationOptions::otherId(),
        ]);
    }

    public function save(Request $request, string $step)
    {
        $user = Auth::user();
        $result = $this->wizard->saveStep($user, $step, $request->except(['_token', '_draft']), $request->boolean('_draft'));
        $user = $user->fresh();
        $status = $this->wizard->status($user);

        return response()->json([
            'ok' => empty($result['errors']),
            'errors' => (object) $result['errors'],
            'percent' => $status['percent'],
            'filled' => $status['filled'],
            'total' => $status['total'],
            'sections' => $status['sections'],
            'missing' => $status['missing'],
            'complete' => $status['complete'],
        ]);
    }

    /** Areas / societies other members of a city have used (suggestions for the "Area / society" box; the member can type their own). */
    public function areas($city)
    {
        $names = User::where('city', (string) $city)->whereNotNull('society')->where('society', '!=', '')
            ->selectRaw('society, count(*) as c')->groupBy('society')->orderByDesc('c')->orderBy('society')->limit(80)->pluck('society')
            ->filter(fn ($n) => preg_match('/^[\p{L}\p{N} .,()&\/\'’\-]{2,60}$/u', $n))->values();
        return response()->json(['areas' => $names]);
    }

    public function finish(Request $request)
    {
        $user = Auth::user();
        if (!$this->wizard->complete($user)) {
            Session::flash('message', 'warning|A few required details are still missing.');
            return redirect()->route('member.complete');
        }
        $user->forceFill(['must_complete_profile' => false, 'profile_completed_at' => $user->profile_completed_at ?: now()])->save();
        User::retrieveUserObject($user->dataid, true);
        Session::flash('message', 'success|Your profile is complete. Welcome to your dashboard!');
        return redirect('member/profile');
    }

    /** The member's own photo for the profile card (null when they have none: the card shows initials). */
    private function avatarOf(User $user): ?string
    {
        try {
            $img = $user->getProfileImage(true);
        } catch (\Throwable $e) {
            return null;
        }
        return ($img && !str_contains($img, '_large')) ? $img : null;
    }

    private function casteOptions($religion): array
    {
        return MasterData::where('type', 'CASTE')
            ->where(fn ($q) => $q->where('subtype', $religion)->orWhereNull('subtype')->orWhere('subtype', ''))
            ->orderByRaw("name = 'Other' ASC")->orderBy('order', 'DESC')->orderBy('name')->pluck('name', 'dataid')->all();
    }

    private function stateOptions($country): array
    {
        return MasterData::where(['type' => 'STATE', 'subtype' => $country])->orderBy('order', 'DESC')->orderBy('name')->pluck('name', 'dataid')->all();
    }

    private function cityOptions($state, $country): array
    {
        if ($state) {
            return MasterData::where(['type' => 'CITY', 'subtype' => $state])->orderBy('order', 'DESC')->orderBy('name')->pluck('name', 'dataid')->all();
        }
        if ($country && !MasterData::where(['type' => 'STATE', 'subtype' => $country])->exists()) {
            return [];
        }
        return [];
    }
}
