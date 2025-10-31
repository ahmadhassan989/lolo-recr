<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a list of applications.
     */
    public function index(): View
    {
        $this->authorize('applications.view');

        $applications = Application::query()
            ->with(['project', 'candidate'])
            ->latest()
            ->paginate(15);

        return view('applications.index', compact('applications'));
    }

    /**
     * Display a single application.
     */
    public function show(Application $application): View
    {
        $this->authorize('applications.view');

        $application->load(['project', 'candidate', 'updatedBy']);

        return view('applications.show', compact('application'));
    }

    /**
     * Update an application's status.
     */
    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('applications.update');

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
}
