<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\CandidateLog;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareersController extends Controller
{
    /**
     * Display a listing of open jobs.
     */
    public function index(): View
    {
        $jobs = Job::query()
            ->where('status', 'open')
            ->where(function ($query) {
                $query->whereNull('deadline')
                    ->orWhereDate('deadline', '>=', now()->toDateString());
            })
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('public.careers.index', compact('jobs'));
    }

    /**
     * Display a single job posting.
     */
    public function show(Job $job): View
    {
        $this->ensureJobIsPublic($job);

        return view('public.careers.show', compact('job'));
    }

    /**
     * Show the application form for a job.
     */
    public function applyForm(Job $job): View
    {
        $this->ensureJobIsPublic($job);

        return view('public.careers.apply', compact('job'));
    }

    /**
     * Handle submission of a job application.
     */
    public function submitApplication(Request $request, Job $job): RedirectResponse
    {
        $this->ensureJobIsPublic($job);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'cover_letter' => ['nullable', 'string'],
        ]);

        $candidate = Candidate::firstOrNew(['email' => strtolower($validated['email'])]);

        $candidate->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? $candidate->phone,
        ]);

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $candidate->cv_file = $path;
        }

        if (! $candidate->exists) {
            $candidate->status = $candidate->status ?? 'active';
        }

        $candidate->save();

        $application = Application::create([
            'project_id' => $job->project_id,
            'job_id' => $job->id,
            'candidate_id' => $candidate->id,
            'status' => 'applied',
            'notes' => $validated['cover_letter'] ?? null,
        ]);

        CandidateLog::create([
            'candidate_id' => $candidate->id,
            'action' => 'Applied for ' . $job->title,
            'performed_by' => null,
            'notes' => 'Application #' . $application->id,
        ]);

        return redirect()->route('careers.thanks');
    }

    /**
     * Display the thank-you page after applying.
     */
    public function thankYou(): View
    {
        return view('public.careers.thanks');
    }

    protected function ensureJobIsPublic(Job $job): void
    {
        abort_if($job->status !== 'open', 404);

        if ($job->deadline && $job->deadline->copy()->endOfDay()->isPast()) {
            abort(404);
        }
    }
}
