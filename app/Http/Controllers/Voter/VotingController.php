<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class VotingController extends Controller
{
    public function index()
    {
        // Get candidates without grouping initially
        $candidates = Candidate::with('faculty')  // Eager load faculty relation
        ->orderBy('position')
            ->orderBy('name')
            ->get();

        $votingStatus = $this->getVotingStatus();

        // Check if user can vote based on email verification
        $canVote = Auth::user()->hasVerifiedEmail();

        return view('dashboard', [
            'candidates' => $candidates,
            'votingStatus' => $votingStatus,
            'canVote' => $canVote
        ]);
    }

    public function vote(Request $request, Candidate $candidate)
    {
        try {
            // Validate the request
            $request->validate([
                'candidate_id' => 'required|exists:candidates,id'
            ]);

            // Check if user's email is verified
            if (!Auth::user()->hasVerifiedEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email before voting.'
                ], 403);
            }

            DB::beginTransaction();

            // Check if user has already voted
            if (Auth::user()->has_voted) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already voted!'
                ], 403);
            }

            // Validate candidate eligibility
            if (!$candidate->is_active || $candidate->campaign_end_date < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This candidate is not eligible for voting.'
                ], 403);
            }

            // Create vote record
            Vote::create([
                'user_id' => Auth::id(),
                'candidate_id' => $candidate->id,
                'ip_address' => $request->ip(),
                'voted_at' => now(),
            ]);

            // Increment candidate votes
            $candidate->increment('votes');

            // Update vote percentage for all candidates
            $this->updateVotePercentages();

            // Update user voting status
            Auth::user()->update(['has_voted' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your vote has been successfully recorded!',
                'redirect' => route('dashboard')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Voting error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording your vote. Please try again.'
            ], 500);
        }
    }

    private function updateVotePercentages()
    {
        $totalVotes = Vote::count();

        if ($totalVotes > 0) {
            $candidates = Candidate::all();
            foreach ($candidates as $candidate) {
                $percentage = ($candidate->votes / $totalVotes) * 100;
                $candidate->update(['vote_percentage' => round($percentage, 2)]);
            }
        }
    }

    public function history()
    {
        $vote = Vote::with(['candidate' => function ($query) {
            $query->select('id', 'name', 'position', 'faculty');
        }])
            ->where('user_id', Auth::id())
            ->first();

        return view('voter.history', compact('vote'));
    }

    private function getVotingStatus(): array
    {
        $totalVoters = \App\Models\User::where('role', 'voter')->count();
        $totalVotesCast = \App\Models\User::where('has_voted', true)->count();

        return [
            'total_voters' => $totalVoters,
            'total_votes_cast' => $totalVotesCast,
            'voting_percentage' => $totalVoters > 0
                ? round(($totalVotesCast / $totalVoters) * 100, 2)
                : 0,
        ];
    }
}
