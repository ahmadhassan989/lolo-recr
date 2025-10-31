<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    /**
     * Display a listing of jobs.
     */
    public function index(): View
    {
        $this->authorize('jobs.view');

        $jobs = Job::query()
            ->with(['project', 'creator'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new job.
     */
    public function create(): View
    {
        $this->authorize('jobs.manage');

        $projects = Project::query()->orderBy('title')->get(['id', 'title']);

        return view('jobs.create', compact('projects'));
    }

    /**
     * Store a newly created job.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('jobs.manage');

        $validated = $this->validatedData($request);
        $validated['created_by'] = $request->user()->id;

        Job::create($validated);

        return redirect()
            ->route('jobs.index')
            ->with('status', 'Job created successfully.');
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job): View
    {
        $this->authorize('jobs.view');

        $job->load(['project', 'creator']);

        return view('jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified job.
     */
    public function edit(Job $job): View
    {
        $this->authorize('jobs.manage');

        $projects = Project::query()->orderBy('title')->get(['id', 'title']);

        return view('jobs.edit', compact('job', 'projects'));
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job): RedirectResponse
    {
        $this->authorize('jobs.manage');

        $validated = $this->validatedData($request, $job->id);
        $job->update($validated);

        return redirect()
            ->route('jobs.show', $job)
            ->with('status', 'Job updated successfully.');
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job): RedirectResponse
    {
        $this->authorize('jobs.manage');

        $job->delete();

        return redirect()
            ->route('jobs.index')
            ->with('status', 'Job deleted successfully.');
    }

    /**
     * Validate request data for storing/updating jobs.
     */
    protected function validatedData(Request $request, ?int $jobId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship'],
            'salary_range' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:open,closed,draft'],
        ]);
    }
}
