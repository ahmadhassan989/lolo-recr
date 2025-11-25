<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Tag;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('candidates.view');

        $user = $request->user();
        $projectIds = $user->accessibleProjectIds();

        $candidates = Candidate::query()
            ->with(['tags:id,name'])
            ->withCount('applications')
            ->when(
                $search = trim((string) $request->input('search', '')),
                function ($query) use ($search) {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('skills', 'like', '%' . $search . '%');
                    });
                }
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->input('status'))
            )
            ->when(
                $request->filled('min_exp'),
                fn ($query) => $query->where('experience_years', '>=', (int) $request->input('min_exp'))
            )
            ->when(
                $projectIds,
                function ($query) use ($projectIds) {
                    if ($projectIds->isEmpty()) {
                        $query->whereRaw('1=0');
                    } else {
                        $ids = $projectIds->all();
                        $query->whereHas('applications', fn ($app) => $app->whereIn('project_id', $ids));
                    }
                }
            )
            ->when(
                $request->filled('project_id'),
                fn ($query) => $query->whereHas('applications', fn ($app) => $app->where('project_id', $request->integer('project_id')))
            )
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        $filterProjects = Project::query()
            ->orderBy('title')
            ->when($projectIds, fn ($query) => $query->whereIn('id', $projectIds->all()))
            ->get(['id', 'title']);

        return view('candidates.index', compact('candidates', 'filterProjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('candidates.manage');

        return view('candidates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:candidates,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'cv_file' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'skills' => ['nullable', 'string'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'availability_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'status' => ['nullable', Rule::in(['active', 'archived', 'blacklisted'])],
        ]);

        $validated['experience_years'] = $validated['experience_years'] ?? 0;
        $validated['rating'] = $validated['rating'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'active';

        Candidate::create($validated + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('candidates.index')
            ->with('status', 'Candidate created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Candidate $candidate): View
    {
        $this->authorize('candidates.view');
        $this->enforceCandidateVisibility($request->user(), $candidate);

        $candidate->load([
            'applications.project',
            'interviews.project',
            'attachments.uploader',
            'tags',
            'logs.user',
            'jobOffers.project',
            'jobOffers.creator',
        ]);

        $projects = Project::orderBy('title')->get(['id', 'title']);
        $allTags = Tag::orderBy('name')->get(['id', 'name']);

        return view('candidates.show', compact('candidate', 'projects', 'allTags'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidate $candidate): View
    {
        $this->authorize('candidates.manage');

        return view('candidates.edit', compact('candidate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidate $candidate): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:candidates,email,' . $candidate->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'cv_file' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'skills' => ['nullable', 'string'],
            'experience_years' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'availability_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'min:0', 'max:5'],
            'status' => ['nullable', Rule::in(['active', 'archived', 'blacklisted'])],
        ]);

        $validated['experience_years'] = $validated['experience_years'] ?? 0;
        $validated['rating'] = $validated['rating'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'active';

        $candidate->update($validated);

        return redirect()
            ->route('candidates.index')
            ->with('status', 'Candidate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $candidate->delete();

        return redirect()
            ->route('candidates.index')
            ->with('status', 'Candidate deleted successfully.');
    }

    protected function enforceCandidateVisibility($user, Candidate $candidate): void
    {
        if (! $user->restrictsToAssignedProjects()) {
            return;
        }

        $projectIds = $user->accessibleProjectIds() ?? collect();

        if ($projectIds->isEmpty()) {
            abort(403);
        }

        $visible = $candidate->applications()
            ->whereIn('project_id', $projectIds->all())
            ->exists();

        if (! $visible) {
            abort(403);
        }
    }
}
