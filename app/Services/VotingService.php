<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vote;

class VotingService
{
    public function getVotingStatus(): array
    {
        $voters = User::where('role', 'voter');

        return [
            'total_voters' => $voters->count(),
            'total_votes_cast' => $voters->where('has_voted', true)->count(),
            'voting_percentage' => $this->calculateVotingPercentage($voters),
        ];
    }

    public function castVote(Candidate $candidate, User $user, string $ipAddress): array
    {
        if ($user->has_voted) {
            return ['success' => false, 'message' => 'You have already voted!'];
        }

        if (!$candidate->is_active) {
            return ['success' => false, 'message' => 'This candidate is not eligible for voting.'];
        }

        if ($this->hasVotedForPosition($user, $candidate->position)) {
            return ['success' => false, 'message' => "You have already voted for the position of {$candidate->position}!"];
        }

        $this->recordVote($candidate, $user, $ipAddress);
        $this->checkAndUpdateVotingStatus($user);

        return ['success' => true];
    }

    private function calculateVotingPercentage($voters): float
    {
        $totalVoters = $voters->count();
        return $totalVoters > 0
            ? round(($voters->where('has_voted', true)->count() / $totalVoters) * 100, 2)
            : 0;
    }

    private function hasVotedForPosition(User $user, string $position): bool
    {
        return Vote::where('user_id', $user->id)
            ->whereHas('candidate', fn($query) => $query->where('position', $position))
            ->exists();
    }

    private function recordVote(Candidate $candidate, User $user, string $ipAddress): void
    {
        Vote::create([
            'user_id' => $user->id,
            'candidate_id' => $candidate->id,
            'ip_address' => $ipAddress,
            'voted_at' => now(),
        ]);

        $candidate->increment('votes');
    }

    private function checkAndUpdateVotingStatus(User $user): void
    {
        $totalPositions = Candidate::select('position')
            ->where('is_active', true)
            ->distinct()
            ->count();

        $userVotedPositions = Vote::where('user_id', $user->id)
            ->whereHas('candidate', fn($query) => $query->where('is_active', true))
            ->count();

        if ($userVotedPositions >= $totalPositions) {
            $user->update(['has_voted' => true]);
        }
    }
}
