<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CandidateRatingController extends Controller
{
    /**
     * Update the rating for the given candidate.
     */
    public function __invoke(Request $request, Candidate $candidate): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:0', 'max:5'],
        ]);

        $candidate->update([
            'rating' => $validated['rating'],
        ]);

        CandidateLog::create([
            'candidate_id' => $candidate->id,
            'action' => 'Updated rating',
            'performed_by' => $request->user()->id,
            'notes' => 'Set rating to ' . $candidate->rating,
        ]);

        return back()->with('status', 'Candidate rating updated.');
    }
}
