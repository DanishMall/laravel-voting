<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * User roles enumeration
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_VOTER = 'voter';
    public const ROLE_CANDIDATE = 'candidate';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'student_id',
        'faculty',
        'position',
        'email',
        'password',
        'role',
        'phone',
        'has_voted',
        'last_login_at',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'has_voted' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is voter
     */
    public function isVoter(): bool
    {
        return $this->role === self::ROLE_VOTER;
    }

    /**
     * Check if user is candidate
     */
    public function isCandidate(): bool
    {
        return $this->role === self::ROLE_CANDIDATE;
    }

    /**
     * Get user's vote relationship
     */
    public function vote(): HasOne
    {
        return $this->hasOne(Vote::class);
    }

    /**
     * Check if user has voted
     */
    public function hasVoted(): bool
    {
        return $this->has_voted || $this->vote()->exists();
    }

    /**
     * Check if user can vote
     */
    public function canVote(): bool
    {
        return $this->isVoter() &&
            $this->hasVerifiedEmail() &&
            !$this->hasVoted() &&
            $this->isEligibleToVote();
    }

    /**
     * Check if user is eligible to vote based on verification and election timing
     */
    public function isEligibleToVote(): bool
    {
        // Check if there's an active election
        $activeElection = Candidate::where('status', 'active')
            ->where('campaign_start_date', '<=', Carbon::now())
            ->where('campaign_end_date', '>=', Carbon::now())
            ->exists();

        return $this->hasVerifiedEmail() && $activeElection;
    }

    /**
     * Get user's faculty details
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty', 'code');
    }

    /**
     * Record user's login timestamp
     */
    public function recordLogin(): void
    {
        $this->update(['last_login_at' => Carbon::now()]);
    }

    /**
     * Mark user as voted
     */
    public function markAsVoted(): void
    {
        $this->update(['has_voted' => true]);
    }

    /**
     * Get available positions for voting based on user's faculty
     */
    public function getAvailablePositions()
    {
        return Position::query()
            ->where('faculty_code', $this->faculty)
            ->orWhere('faculty_code', null)
            ->get();
    }

    /**
     * Get voting history for the user
     */
    public function getVotingHistory()
    {
        return $this->vote()
            ->with(['candidate' => function ($query) {
                $query->select('id', 'name', 'position', 'faculty');
            }])
            ->first();
    }

    /**
     * Check if email needs verification before voting
     */
    public function needsEmailVerification(): bool
    {
        return !$this->hasVerifiedEmail() && $this->isVoter();
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Set default role if not specified
            if (!$user->role) {
                $user->role = self::ROLE_VOTER;
            }
        });
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

}
