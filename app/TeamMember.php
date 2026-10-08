<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * A matchmaker. Completely separate from `users`: own table, own login (the
 * `team` auth guard, /team/login) and own notifications. A team member never
 * has a member profile; they apply through "Become a Partner", an admin
 * approves them, and they then work in the Team Dashboard.
 *
 * It answers the few User methods the Team Dashboard views call (dataid,
 * getProfileImage(), isAdmin() ...) so those views work for either.
 */
class TeamMember extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Notifiable;

    public const PHOTO_PATH = 'team-members';

    protected $table = 'team_members';

    protected $fillable = [
        'dataid', 'first_name', 'last_name', 'email', 'contact_mobile_number', 'city', 'password', 'photo', 'logo', 'watermark_text', 'watermark_style', 'can_view_originals',
        'is_admin', 'is_approved', 'status', 'application_status', 'experience', 'about_me', 'approved_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_admin' => 'boolean',
        'can_view_originals' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public static function newDataid(): string
    {
        return strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9));
    }

    /**
     * An admin's own team account — created (or brought up to date) on demand, so an admin never
     * needs to be set up by hand. Copies the admin's name, email, mobile number and password hash,
     * so the same sign-in details work on both sides. An existing row keeps its own password.
     */
    public static function provisionForAdmin(User $admin): self
    {
        $member = static::where('email', $admin->email)->first() ?: new static([
            'dataid' => static::newDataid(),
            'email' => $admin->email,
            'password' => $admin->password,
        ]);

        $member->fill([
            'first_name' => $admin->first_name ?: 'Admin',
            'last_name' => $admin->last_name ?: '',
            'contact_mobile_number' => $admin->contact_mobile_number ?: '',
            'is_admin' => true,
            'is_approved' => true,
            'status' => 'active',
            'application_status' => 'approved',
        ]);
        if (!$member->approved_at) {
            $member->approved_at = now();
        }
        $member->save();

        return $member;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\TeamPasswordReset($token));
    }

    /** Approved by an admin (whatever their current status). */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /** Approved and not suspended/deactivated — allowed into the Team Dashboard. */
    public function scopeActive($query)
    {
        return $query->where('is_approved', true)->where('status', 'active');
    }

    /** Applications waiting for an admin decision. */
    public function scopePending($query)
    {
        return $query->where('application_status', 'pending');
    }

    /** A team member who has added MORE than this many proposals gets the "Premium Profile" badge. */
    public const PREMIUM_AFTER_PROPOSALS = 100;

    /** Premium Profile badge: more than 100 proposals added. Counted once per request and cached for 10 minutes. */
    public static function isPremiumId($id): bool
    {
        static $memo = [];
        $id = (int) $id;
        if (!$id) {
            return false;
        }
        return $memo[$id] ??= \Illuminate\Support\Facades\Cache::remember(
            'tm_premium_' . $id,
            600,
            fn () => Proposal::where('added_by', $id)->count() > self::PREMIUM_AFTER_PROPOSALS
        );
    }

    public function isPremium(): bool
    {
        return self::isPremiumId($this->id);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'added_by');
    }

    public function isActiveMember(): bool
    {
        return $this->is_approved && $this->status === 'active';
    }

    /** An admin's own team account (see AdminTeamMemberSeeder): works like any member, plus the management pages. */
    /** Sees every proposal's photos without the watermark: admins always, other members when an admin allowed it. */
    public function seesOriginalPhotos(): bool
    {
        return $this->isAdmin() || (bool) $this->can_view_originals;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /** Why this account can't sign in yet, or null when it can. Flash-message format ("type|text"). */
    public function loginBlockMessage(): ?string
    {
        if ($this->application_status === 'pending') {
            return 'warning|Your application is still under review. We\'ll notify you once it\'s approved.';
        }
        if ($this->application_status === 'rejected') {
            return 'danger|Your application was not approved. Please contact support at 0304-0227000.';
        }
        if ($this->status === 'suspended') {
            return 'warning|Your account is suspended. Please contact support at 0304-0227000.';
        }
        if ($this->status !== 'active' || !$this->is_approved) {
            return 'danger|Your account is not active. Please contact support at 0304-0227000.';
        }
        return null;
    }

    /** Own profile photo (or the default avatar). $tiny is accepted for compatibility with User::getProfileImage(). */
    public function getProfileImage($tiny = null)
    {
        if ($this->photo) {
            $prefix = $tiny ? 'thumbnail_sm_' : 'thumbnail_';
            $file = public_path(self::PHOTO_PATH . '/' . $prefix . $this->photo);
            if (file_exists($file)) {
                return '/' . self::PHOTO_PATH . '/' . $prefix . $this->photo . '?v=' . filemtime($file);
            }
        }
        return Profile::defaultImage('male');
    }

    /** How many AI matches the proposals this member owns have in total (sidebar badge). */
    public function ownedProposalsAiMatchesCount(): int
    {
        // Distinct profiles: one that fits several of this member's clients is still one AI match.
        $ids = [];
        foreach (Proposal::where('added_by', $this->id)->get() as $proposal) {
            foreach (array_keys($proposal->strongMatchScores()) as $id) {
                $ids[$id] = true;
            }
        }
        return count($ids);
    }

    /** Cards on the My Matches page: one per (own client, match) pair, up to 20 matches per client. */
    public function myMatchesPairCount(): int
    {
        $total = 0;
        foreach (Proposal::where('added_by', $this->id)->get() as $proposal) {
            $total += min(20, count($proposal->strongMatchScores()));
        }
        return $total;
    }
}
