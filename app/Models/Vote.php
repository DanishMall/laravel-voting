<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'candidate_id',
        'ip_address',
        'voted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'voted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the vote.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the candidate that received the vote.
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Scope a query to filter votes by faculty.
     */
    public function scopeByFaculty($query, $faculty)
    {
        return $query->whereHas('candidate', function ($q) use ($faculty) {
            $q->where('faculty', $faculty);
        });
    }

    /**
     * Scope a query to filter votes by position.
     */
    public function scopeByPosition($query, $position)
    {
        return $query->whereHas('candidate', function ($q) use ($position) {
            $q->where('position', $position);
        });
    }

    /**
     * Scope a query to get votes within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('voted_at', [$startDate, $endDate]);
    }

    /**
     * Get the vote date in a human-readable format.
     */
    public function getVoteDateAttribute()
    {
        return $this->voted_at->format('F j, Y g:i A');
    }
}
