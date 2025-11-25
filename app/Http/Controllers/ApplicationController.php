<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a list of applications.
     */
    public function index(Request $request): View
    {
        $this->authorize('applications.view');

        $user = $request->user();
        $projectIds = $user->accessibleProjectIds();

        $applications = Application::query()
            ->with(['project', 'candidate'])
            ->when($projectIds, fn ($query) => $query->whereIn('project_id', $projectIds->all()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('project_id'), fn ($query) => $query->where('project_id', $request->integer('project_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%' . trim($request->input('search')) . '%';
                $query->whereHas('candidate', function ($candidateQuery) use ($term) {
                    $candidateQuery->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('date_to')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $projectOptions = Project::query()
            ->orderBy('title')
            ->when($projectIds, fn ($query) => $query->whereIn('id', $projectIds->all()))
            ->get(['id', 'title']);

        $filters = $request->only(['status', 'project_id', 'search', 'date_from', 'date_to']);

        return view('applications.index', compact('applications', 'projectOptions', 'filters'));
    }

    /**
     * Display a single application.
     */
    public function show(Application $application): View
    {
        $this->authorize('applications.view');
        $this->enforceApplicationVisibility(request()->user(), $application);

        $application->load(['project', 'candidate', 'updatedBy']);

        return view('applications.show', compact('application'));
    }

    /**
     * Update an application's status.
     */
    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('applications.update');
        $this->enforceApplicationVisibility($request->user(), $application);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['applied', 'screening', 'interview', 'offer', 'hired', 'rejected'])],
            'notes' => ['nullable', 'string'],
        ]);

        $application->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $application->notes,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('applications.show', $application)
            ->with('status', 'Application status updated.');
    }

    protected function enforceApplicationVisibility($user, Application $application): void
    {
        if (! $user->restrictsToAssignedProjects()) {
            return;
        }

        $projectIds = $user->accessibleProjectIds() ?? collect();

        if ($projectIds->isEmpty() || ! $projectIds->contains($application->project_id)) {
            abort(403);
        }
    }
}
