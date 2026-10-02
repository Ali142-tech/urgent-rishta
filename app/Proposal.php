<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A client proposal curated by a matchmaker (team member). Lives in its own
 * `proposals` table — see the create_proposals_tables migration — instead of
 * being a login-less row in `users`.
 *
 * To keep the existing team screens working with few changes, this model offers
 * the same read API those screens already used on Profile/User rows:
 *   dataid (= reference, e.g. P-001), lbl_* display labels, con_of_residence_code,
 *   images / getProfileImage() / getCardImages() / getLightGalleryImages(),
 *   profile() (returns itself), and a `preference` object with the partner
 *   requirements. It also owns the AI matching (candidates + compatibility score).
 */
class Proposal extends Model
{
    use SoftDeletes;

    /** Public folder (under /public) the proposal photos are stored in. */
    const PHOTO_PATH = '/proposals';

    protected $table = 'proposals';

    protected $fillable = [
        'reference', 'added_by',
        'gender', 'birthday', 'height', 'marital_status', 'education', 'profession', 'caste_id', 'sect',
        'con_of_residence', 'con_of_citizenship', 'current_city', 'city',
        'family_status', 'looking_from', 'presentation_highlight',
        'profile_status', 'active', 'raw_intake_text',
        'pref_age_min', 'pref_age_max', 'pref_height', 'pref_city', 'pref_profession', 'pref_note',
    ];

    protected $casts = [
        'birthday' => 'date',
        'active' => 'boolean',
    ];

    /** Per-request lookup caches (masterdata names are read many times per page). */
    private static array $nameCache = [];
    private static ?array $casteCache = null;

    protected static function booted()
    {
        // P-001, P-002 ... derived from the row id: unique, readable, never reused.
        static::created(function (Proposal $proposal) {
            if (empty($proposal->reference)) {
                $proposal->forceFill(['reference' => self::formatReference($proposal->id)])->saveQuietly();
            }
        });
    }

    public static function formatReference(int $id): string
    {
        return 'P-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    // ------------------------------------------------------------------
    // relations
    // ------------------------------------------------------------------

    public function owner()
    {
        return $this->belongsTo(TeamMember::class, 'added_by');
    }

    public function caste()
    {
        return $this->belongsTo(ProposalCaste::class, 'caste_id');
    }

    public function photos()
    {
        return $this->hasMany(ProposalPhoto::class)->orderByDesc('is_main')->orderBy('sort_order')->orderBy('id');
    }

    // ------------------------------------------------------------------
    // compatibility accessors (what the old Profile/User rows exposed)
    // ------------------------------------------------------------------

    public function getDataidAttribute()
    {
        return $this->reference;
    }

    // Proposals carry no personal name — the reference is the label everywhere.
    public function getFirstNameAttribute() { return null; }
    public function getLastNameAttribute() { return null; }
    public function getNameAttribute() { return $this->reference; }
    public function getPhotoVerificationStatusAttribute() { return null; }
    public function getProfileCategoryAttribute() { return null; }

    public function getAgeAttribute(): ?int
    {
        return $this->birthday ? $this->birthday->age : null;
    }

    private static function masterName(string $type, $dataid): ?string
    {
        if ($dataid === null || $dataid === '') {
            return null;
        }
        if (!isset(self::$nameCache[$type])) {
            self::$nameCache[$type] = MasterData::where('type', $type)->get(['dataid', 'name', 'abbreviation'])
                ->keyBy('dataid')->all();
        }
        return self::$nameCache[$type][$dataid]->name ?? null;
    }

    private static function masterAbbreviation(string $type, $dataid): ?string
    {
        if ($dataid === null || $dataid === '') {
            return null;
        }
        self::masterName($type, $dataid);   // warms the cache
        return self::$nameCache[$type][$dataid]->abbreviation ?? null;
    }

    public function getLblMaritalStatusAttribute() { return self::masterName('MARITAL_STATUS', $this->marital_status); }
    public function getLblEducationAttribute() { return self::masterName('EDUCATION', $this->education); }
    public function getLblConOfResidenceAttribute() { return self::masterName('COUNTRY', $this->con_of_residence); }
    public function getConOfResidenceCodeAttribute() { return self::masterAbbreviation('COUNTRY', $this->con_of_residence); }
    public function getLblConOfCitizenshipAttribute() { return self::masterName('COUNTRY', $this->con_of_citizenship); }
    public function getConOfCitizenshipCodeAttribute() { return self::masterAbbreviation('COUNTRY', $this->con_of_citizenship); }
    public function getLblConOfBirthAttribute() { return null; }
    public function getConOfBirthCodeAttribute() { return null; }
    public function getLblCityAttribute() { return $this->city; }
    public function getLblReligionAttribute() { return null; }
    public function getLblMotherTongueAttribute() { return null; }
    public function getLblPackageAttribute() { return null; }

    public function getLblCasteAttribute()
    {
        if (empty($this->caste_id)) {
            return null;
        }
        if (self::$casteCache === null) {
            self::$casteCache = ProposalCaste::pluck('name', 'id')->all();
        }
        return self::$casteCache[$this->caste_id] ?? null;
    }

    /** Screens call $x->profile() on both users and proposals; a proposal IS its profile. */
    public function profile($refresh = null)
    {
        return $this;
    }

    public function isOnline(): bool
    {
        return false;
    }

    public function hasPartnerPreferences(): bool
    {
        return $this->pref_age_min || $this->pref_age_max
            || !empty($this->pref_height) || !empty($this->pref_city) || !empty($this->pref_profession);
    }

    /**
     * Partner requirements as an object shaped like the old PartnerPreference row, so
     * screens that read $preference->age_min / ->pref_city / ->general_requirement ... keep
     * working. Null when nothing is filled in.
     */
    public function getPreferenceAttribute()
    {
        if (!$this->hasPartnerPreferences() && empty($this->pref_note)) {
            return null;
        }
        return (object) [
            'age_min' => $this->pref_age_min,
            'age_max' => $this->pref_age_max,
            'height' => $this->pref_height,
            'pref_height' => $this->pref_height,
            'pref_city' => $this->pref_city,
            'profession' => $this->pref_profession,
            'general_requirement' => $this->pref_note,
        ];
    }

    /** Proposals that have at least one partner requirement filled in (usable for AI matching). */
    public function scopeWithPreferences($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('pref_age_min')->orWhereNotNull('pref_age_max')
                ->orWhereNotNull('pref_height')->orWhereNotNull('pref_city')->orWhereNotNull('pref_profession');
        });
    }
    // ------------------------------------------------------------------
    // photos
    // ------------------------------------------------------------------

    /** File names of this proposal's photos (empty array when there are none). */
    public function getImagesAttribute(): array
    {
        return $this->photos->pluck('file_name')->all();
    }

    private function photoThumbsOrNull(bool $small = false): array
    {
        $paths = [];
        foreach ($this->photos as $photo) {
            $path = $small ? $photo->thumb_sm_path : $photo->thumb_path;
            if (file_exists(public_path($path))) {
                $paths[] = $path;
            } elseif (file_exists(public_path($photo->path))) {
                $paths[] = $photo->path;
            }
        }
        return $paths;
    }

    public function getProfileImage($tiny = null, $watermarked = false)
    {
        $paths = $this->photoThumbsOrNull((bool) $tiny);
        return $paths[0] ?? Profile::defaultImage($this->gender);
    }

    public function getCardImages($tiny = null, $watermarked = false)
    {
        $paths = $this->photoThumbsOrNull((bool) $tiny);
        return $paths ?: [Profile::defaultImage($this->gender)];
    }

    /** JSON string of ['src','thumb','mobileSrc'] for each photo (same shape Profile gave). */
    public function getLightGalleryImages($watermarked = false)
    {
        $out = [];
        foreach ($this->photos as $photo) {
            if (!file_exists(public_path($photo->path))) {
                continue;
            }
            $thumb = file_exists(public_path($photo->thumb_path)) ? $photo->thumb_path : $photo->path;
            $out[] = ['src' => $photo->path, 'thumb' => $thumb, 'mobileSrc' => $photo->path];
        }
        return json_encode($out);
    }

    // ------------------------------------------------------------------
    // AI matching
    // ------------------------------------------------------------------

    /** Opposite-gender, active proposals (not this one) that fit this one's age range. */
    public function candidatesQuery()
    {
        $own = strtolower($this->gender ?? '');
        $opposite = $own === 'male' ? 'female' : ($own === 'female' ? 'male' : null);
        if ($opposite === null || !$this->hasPartnerPreferences()) {
            return null;
        }

        $query = static::query()->with('photos')
            ->where('gender', $opposite)
            ->where('active', true)
            ->where('id', '!=', $this->id);

        if ($this->pref_age_min || $this->pref_age_max) {
            $min = $this->pref_age_min ?: 18;
            $max = $this->pref_age_max ?: 99;
            $query->whereNotNull('birthday')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN ? AND ?', [(int) $min, (int) $max]);
        }

        // Same city / same country first, then the most recently updated.
        if (!empty($this->city)) {
            $query->orderByRaw('(city = ?) DESC', [$this->city]);
        }
        if (!empty($this->con_of_residence)) {
            $query->orderByRaw('(con_of_residence = ?) DESC', [$this->con_of_residence]);
        }
        return $query->orderByDesc('updated_at');
    }

    public function getProposalMatches($limit = 3, $offset = 0)
    {
        $query = $this->candidatesQuery();
        if ($query === null) {
            return collect();
        }
        return $query->skip((int) $offset)->take((int) $limit)->get();
    }

    public function getProposalMatchesCount(): int
    {
        $query = $this->candidatesQuery();
        return $query === null ? 0 : (int) $query->count();
    }

    /** "5'6", "5.6", "5 ft 6", "5-3" -> total inches; null when it can't be read. */
    public static function heightToInches($text): ?int
    {
        if (!is_string($text) || !preg_match("/(\d)\s*(?:'|ft|feet|foot|\.|-|\s)?\s*(\d{1,2})?/i", $text, $m)) {
            return null;
        }
        $feet = (int) $m[1];
        if ($feet < 4 || $feet > 7) {
            return null;
        }
        $inches = isset($m[2]) && $m[2] !== '' ? (int) $m[2] : 0;
        return $inches <= 11 ? $feet * 12 + $inches : $feet * 12;
    }

    /**
     * How well THIS proposal (the candidate) fits what $client is looking for.
     * Returns null when the client has nothing usable to score against, else
     * ['percent', 'matched', 'total', 'checks' => [['factor','label','matched','score'], ...]].
     * The overall percent is a weighted average of each factor's own 0-100 score;
     * weights come from the admin "Match weights" page (a missing factor counts as 1).
     */
    public function compatibilityWith(Proposal $client): ?array
    {
        $checks = [];

        // Age — graduated: a year outside the range reads very differently from fifteen.
        $hasAgePref = $client->pref_age_min || $client->pref_age_max;
        $clientAge = $client->age;
        if ($hasAgePref || $clientAge !== null) {
            $ageMin = $hasAgePref ? $client->pref_age_min : max(18, $clientAge - 7);
            $ageMax = $hasAgePref ? $client->pref_age_max : min(99, $clientAge + 7);
            $age = $this->age;
            $matched = $age !== null && (empty($ageMin) || $age >= $ageMin) && (empty($ageMax) || $age <= $ageMax);
            $score = 0;
            if ($age !== null) {
                if ($matched) {
                    $score = 100;
                } else {
                    $distance = (!empty($ageMin) && $age < $ageMin) ? $ageMin - $age : (($age > $ageMax) ? $age - $ageMax : 0);
                    $score = max(0, 100 - $distance * 15);   // -15% per year outside the range
                }
            }
            $checks[] = ['factor' => 'Age', 'label' => $hasAgePref ? 'Preferred age range matched' : 'Similar age', 'matched' => (bool) $matched, 'score' => (int) round($score)];
        }

        // Location — preferred city (free text) first; same country as the client is the fallback.
        if (!empty($client->pref_city) || !empty($client->con_of_residence)) {
            $cityMatched = false;
            if (!empty($client->pref_city)) {
                $wanted = mb_strtolower(trim($client->pref_city));
                foreach ([$this->city, $this->current_city] as $c) {
                    $c = mb_strtolower(trim((string) $c));
                    if ($c !== '' && ($c === $wanted || str_contains($wanted, $c) || str_contains($c, $wanted))) {
                        $cityMatched = true;
                        break;
                    }
                }
            }
            $countryMatched = !empty($client->con_of_residence) && $this->con_of_residence == $client->con_of_residence;
            $matched = $cityMatched || $countryMatched;
            $score = $cityMatched ? 100 : ($countryMatched ? 65 : 20);
            $checks[] = ['factor' => 'Location', 'label' => $cityMatched ? 'Preferred city matched' : ($countryMatched ? 'Same country' : 'Location differs'), 'matched' => (bool) $matched, 'score' => $score];
        }

        // Profession — the preferred profession (substring) when set, else the client's own.
        if (!empty($client->pref_profession)) {
            $matched = !empty($this->profession) && stripos($this->profession, $client->pref_profession) !== false;
            $checks[] = ['factor' => 'Profession', 'label' => 'Preferred profession matched', 'matched' => $matched, 'score' => $matched ? 100 : 0];
        } elseif (!empty($client->profession) && !empty($this->profession)) {
            $matched = strcasecmp(trim($this->profession), trim($client->profession)) === 0;
            $checks[] = ['factor' => 'Profession', 'label' => 'Same profession', 'matched' => $matched, 'score' => $matched ? 100 : 0];
        }

        // Height — "5'3 or above" means at least that tall; otherwise within 3 inches.
        $wantedHeight = self::heightToInches($client->pref_height);
        $candidateHeight = self::heightToInches($this->height);
        if ($wantedHeight !== null && $candidateHeight !== null) {
            $atLeast = (bool) preg_match('/above|taller|more|\+/i', (string) $client->pref_height);
            $matched = $atLeast ? $candidateHeight >= $wantedHeight : abs($candidateHeight - $wantedHeight) <= 3;
            $checks[] = ['factor' => 'Height', 'label' => 'Preferred height matched', 'matched' => $matched, 'score' => $matched ? 100 : 0];
        }

        // Nationality — same citizenship as the client.
        if (!empty($client->con_of_citizenship)) {
            $matched = $this->con_of_citizenship == $client->con_of_citizenship;
            $checks[] = ['factor' => 'Nationality', 'label' => 'Same nationality', 'matched' => $matched, 'score' => $matched ? 100 : 0];
        }

        if (empty($checks)) {
            return null;
        }

        $weights = \App\MatchWeightSetting::current();
        $weightedSum = 0;
        $weightTotal = 0;
        foreach ($checks as $check) {
            $weight = $weights[$check['factor']] ?? 1;
            $weightedSum += $check['score'] * $weight;
            $weightTotal += $weight;
        }

        return [
            'percent' => $weightTotal > 0 ? (int) round($weightedSum / $weightTotal) : 0,
            'matched' => count(array_filter($checks, fn ($c) => $c['matched'])),
            'total' => count($checks),
            'checks' => $checks,
        ];
    }
}
