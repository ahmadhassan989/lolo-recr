<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class InterviewController extends Controller
{
    /**
     * Store a newly created interview for the candidate.
     */
    public function store(Request $request, Candidate $candidate): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'interview_date' => ['required', 'date'],
            'interviewer' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['hr', 'technical', 'final'])],
            'result' => ['required', Rule::in(['pending', 'passed', 'failed'])],
            'notes' => ['nullable', 'string'],
        ]);

        $candidate->interviews()->create($validated);

        return back()->with('status', 'Interview logged successfully.');
    }

    /**
     * Update the specified interview.
     */
    public function update(Request $request, Interview $interview): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'interview_date' => ['required', 'date'],
            'interviewer' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['hr', 'technical', 'final'])],
            'result' => ['required', Rule::in(['pending', 'passed', 'failed'])],
            'notes' => ['nullable', 'string'],
        ]);

        $interview->update($validated);

        return back()->with('status', 'Interview updated successfully.');
    }

    /**
     * Remove the specified interview.
     */
    public function destroy(Interview $interview): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $interview->delete();

        return back()->with('status', 'Interview removed.');
    }
}
