<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $candidates = Candidate::withCount('votes')
            ->with('faculty')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $votingStatus = [
            'total_voters' => DB::table('users')->where('can_vote', true)->count(),
            'total_votes_cast' => DB::table('votes')->distinct('user_id')->count(),
            'voting_percentage' => $this->calculateVotingPercentage(),
        ];

        return view('admin.dashboard', compact('candidates', 'votingStatus'));
    }

    public function create()
    {
        $faculties = Faculty::orderBy('name')->get();
        return view('admin.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:candidates,student_id',
            'name' => 'required|string|max:255',
            'faculty' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'campaign_start_date' => 'nullable|date',
            'campaign_end_date' => 'nullable|date|after:campaign_start_date',
        ]);

        try {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imagePath = $request->file('image')->store('candidates', 'public');
                $validated['image_path'] = $imagePath;
            }

            Candidate::create($validated);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Candidate created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to upload image. Please try again.');
        }
    }

    public function edit(Candidate $candidate)
    {
        return view('admin.edit', compact('candidate'));
    }

    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:candidates,student_id,' . $candidate->id,
            'name' => 'required|string|max:255',
            'faculty' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'campaign_start_date' => 'nullable|date',
            'campaign_end_date' => 'nullable|date|after:campaign_start_date',
        ]);

        try {
            // Set is_active status - if checkbox not checked, it won't be in request
            $validated['is_active'] = $request->has('is_active');

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image if exists
                if ($candidate->image_path) {
                    Storage::disk('public')->delete($candidate->image_path);
                }

                // Store new image
                $imagePath = $request->file('image')->store('candidates', 'public');
                $validated['image_path'] = $imagePath;
            }

            $candidate->update($validated);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Candidate updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update candidate. Please try again.');
        }
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->image_path) {
            Storage::disk('public')->delete($candidate->image_path);
        }

        $candidate->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Candidate deleted successfully.');
    }

    public function toggleStatus(Candidate $candidate)
    {
        $candidate->update([
            'is_active' => !$candidate->is_active
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Candidate status updated successfully.');
    }

    public function exportCandidates()
    {
        $candidates = Candidate::with('faculty')
            ->withCount('votes')
            ->get();

        return response()->streamDownload(function () use ($candidates) {
            $csv = fopen('php://output', 'w');

            // Add headers
            fputcsv($csv, [
                'Student ID',
                'Name',
                'Faculty',
                'Position',
                'Votes',
                'Vote Percentage',
                'Status',
                'Campaign Period'
            ]);

            // Add data
            foreach ($candidates as $candidate) {
                fputcsv($csv, [
                    $candidate->student_id,
                    $candidate->name,
                    $candidate->faculty,
                    $candidate->position,
                    $candidate->votes_count,
                    $candidate->vote_percentage . '%',
                    $candidate->is_active ? 'Active' : 'Inactive',
                    $candidate->campaign_start_date . ' to ' . $candidate->campaign_end_date
                ]);
            }

            fclose($csv);
        }, 'candidates.csv');
    }

    private function calculateVotingPercentage()
    {
        $totalVoters = DB::table('users')->where('can_vote', true)->count();
        if ($totalVoters === 0) return 0;

        $totalVotesCast = DB::table('votes')->distinct('user_id')->count();
        return round(($totalVotesCast / $totalVoters) * 100, 2);
    }

}
