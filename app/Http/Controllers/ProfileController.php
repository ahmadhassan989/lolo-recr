<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('roles');

        $assignedProjects = $user->projectsAssigned()
            ->withCount('applications')
            ->orderBy('title')
            ->get();

        $teamLeadProjects = Project::query()
            ->where('team_lead_id', $user->id)
            ->withCount('applications')
            ->orderBy('title')
            ->get();

        $stats = [
            'assigned_projects' => $assignedProjects->count(),
            'leading_projects' => $teamLeadProjects->count(),
            'applications_updated' => $user->updatedApplications()->count(),
        ];

        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
            'assignedProjects' => $assignedProjects,
            'teamLeadProjects' => $teamLeadProjects,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
