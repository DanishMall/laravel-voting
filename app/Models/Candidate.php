<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    use HasFactory;
    protected $table = 'candidates';
    protected $fillable = [
        'student_id',
        'name',
        'faculty',
        'position',
        'vision',
        'mission',
        'image_path',
        'is_active',
        'campaign_start_date',
        'campaign_end_date',
    ];
    protected $casts = [
        'votes' => 'integer',
        'is_active' => 'boolean',
        'campaign_start_date' => 'date',
        'campaign_end_date' => 'date',
    ];

    // Relationships
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? Storage::url($this->image_path)
            : '/images/default.png';
    }

//    public function getImageUrlAttribute()
//    {
//        // Check if the image_path is set and exists in storage
//        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
//            return Storage::url($this->image_path); // Return the URL for the uploaded image
//        }
//
//        // Use a default image based on the candidate's position
//        $defaultImages = [
//            'President' => '/images/default.png',
//            'Vice President' => '/images/default.png',
//            'Secretary' => '/images/default.png',
//            // Add other roles if applicable
//        ];
//
//        // Return the default image based on the position or a generic default
//        return $defaultImages[$this->position] ?? '/images/default.png';
//    }


    public function getVotePercentageAttribute()
    {
        $totalVotes = static::sum('votes');
        return $totalVotes > 0
            ? round(($this->votes / $totalVotes) * 100, 2)
            : 0;
    }

    public function getCampaignPeriodAttribute()
    {
        if (!$this->campaign_start_date || !$this->campaign_end_date) {
            return 'N/A';
        }

        return $this->campaign_start_date->format('d M Y') . ' to ' . $this->campaign_end_date->format('d M Y');
    }

    // Scopes
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeFaculty($query, $faculty)
    {
        return $query->where('faculty', $faculty);
    }

    // Methods
    public function resetVotes()
    {
        $this->update(['votes' => 0]);
    }

    public function incrementVotes()
    {
        return $this->increment('votes');
    }

}
