<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('projects.view');

        $projects = Project::query()
            ->withCount('applications')
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('projects.manage');

        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('projects.manage');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:open,closed'],
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
        $this->authorize('projects.view');

        $project->load(['applications.candidate']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View
    {
        $this->authorize('projects.manage');

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('projects.manage');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:open,closed'],
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
        $this->authorize('projects.manage');

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', 'Project deleted successfully.');
    }
}
