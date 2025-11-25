<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $user = $request->user();

        $projects = Project::query()
            ->with(['teamLead'])
            ->withCount('applications')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . trim($request->input('search')) . '%';
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', $search)
                        ->orWhere('department', 'like', $search);
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('department'), fn ($query) => $query->where('department', $request->input('department')))
            ->when($request->filled('team_lead_id'), fn ($query) => $query->where('team_lead_id', $request->integer('team_lead_id')))
            ->when(
                ! ($user->hasRole('super_admin') || $user->can('projects.manage')),
                function ($query) use ($user) {
                    $query->where(function ($inner) use ($user) {
                        $inner->whereHas('team', fn ($teamQuery) => $teamQuery->where('user_id', $user->id))
                            ->orWhere('team_lead_id', $user->id);
                    });
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $departments = Project::query()
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $teamLeads = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $filters = $request->only(['search', 'status', 'department', 'team_lead_id']);

        return view('projects.index', compact('projects', 'departments', 'teamLeads', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create', [
            'teamLeadOptions' => $this->teamLeadOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:open,closed'],
            'team_lead_id' => ['nullable', 'exists:users,id'],
        ]);

        $request->user()->projects()->create($validated);

        return redirect()
            ->route('projects.index')
            ->with('status', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load(['applications.candidate', 'teamLead']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', [
            'project' => $project,
            'teamLeadOptions' => $this->teamLeadOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:open,closed'],
            'team_lead_id' => ['nullable', 'exists:users,id'],
        ]);

        $project->update($validated);

        return redirect()
            ->route('projects.index')
            ->with('status', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', 'Project deleted successfully.');
    }

    protected function teamLeadOptions()
    {
        return User::orderBy('name')->get(['id', 'name', 'email']);
    }
}
