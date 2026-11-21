<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\CandidateLog;
use App\Models\Job;
use App\Notifications\ApplicationReceived;
use App\Notifications\NewApplicationAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class CareersController extends Controller
{
    /**
     * Display a listing of open jobs.
     */
    public function index(Request $request): View
    {
        $openJobs = Job::query()
            ->where('status', 'open')
            ->where(function ($query) {
                $query->whereNull('deadline')
                    ->orWhereDate('deadline', '>=', now()->toDateString());
            });

        $jobs = (clone $openJobs)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';

                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                        ->orWhere('department', 'like', $term)
                        ->orWhere('skills', 'like', $term);
                });
            })
            ->when($request->filled('department'), fn ($query) => $query->where('department', $request->department))
            ->when($request->filled('type'), fn ($query) => $query->where('employment_type', $request->type))
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        $departments = (clone $openJobs)->whereNotNull('department')->distinct()->orderBy('department')->pluck('department');
        $types = (clone $openJobs)->whereNotNull('employment_type')->distinct()->orderBy('employment_type')->pluck('employment_type');

        return view('public.careers.index', compact('jobs', 'departments', 'types'));
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

        $application = Application::firstOrCreate(
            ['candidate_id' => $candidate->id, 'job_id' => $job->id],
            [
                'project_id' => $job->project_id,
                'status' => 'applied',
                'notes' => $validated['cover_letter'] ?? null,
            ]
        );

        if ($application->wasRecentlyCreated) {
            CandidateLog::create([
                'candidate_id' => $candidate->id,
                'action' => 'Applied for '.$job->title,
                'performed_by' => null,
                'notes' => 'Application #'.$application->id,
            ]);

            try {
                $candidate->notify(new ApplicationReceived($application));
                $this->alertProjectTeam($application);
            } catch (\Throwable $e) {
                report($e);
            }
        }

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

    protected function alertProjectTeam(Application $application): void
    {
        $project = $application->project;
        if (! $project) {
            return;
        }

        $recipients = collect([$project->teamLead])
            ->merge($project->team->map->user)
            ->filter()
            ->unique('id');

        Notification::send($recipients, new NewApplicationAlert($application));
    }
}
