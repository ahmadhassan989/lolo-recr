<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CandidateTagController extends Controller
{
    /**
     * Sync the selected tags for the candidate.
     */
    public function __invoke(Request $request, Candidate $candidate): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $validated = $request->validate([
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        $candidate->tags()->sync($validated['tags'] ?? []);

        CandidateLog::create([
            'candidate_id' => $candidate->id,
            'action' => 'Updated tags',
            'performed_by' => $request->user()->id,
            'notes' => 'Tags synced: ' . $candidate->tags()->pluck('name')->implode(', '),
        ]);

        return back()->with('status', 'Tags updated.');
    }
}
