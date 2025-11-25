<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class ProjectTeamController extends Controller
{
    public function index()
    {
        $projects = Project::with(['team.user.roles', 'teamLead'])->orderBy('title')->paginate(10);

        return view('admin.projects.team.index', compact('projects'));
    }

    public function edit(Project $project)
    {
        $users = User::orderBy('name')->get();

        return view('admin.projects.team.edit', [
            'project' => $project->load(['team.user', 'teamLead']),
            'users' => $users,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'members' => ['nullable', 'array'],
            'members.*.user_id' => ['required', 'distinct', Rule::exists('users', 'id')],
            'members.*.role' => ['required', Rule::in(['hr', 'recruiter'])],
            'team_lead_id' => ['nullable', Rule::exists('users', 'id')],
        ]);

        $members = $validated['members'] ?? [];
        $memberIds = collect($members)->pluck('user_id');
        $selectedLead = $request->input('team_lead_id');

        if ($selectedLead && ! $memberIds->contains((int) $selectedLead)) {
            return back()
                ->withErrors(['team_lead_id' => 'Team lead must be one of the assigned team members.'])
                ->withInput();
        }

        $existingAssignments = $project->team()->get();

        foreach ($existingAssignments as $assignment) {
            $this->removeProjectRole($assignment);
        }

        $project->team()->delete();

        foreach ($members as $member) {
            $projectUser = $project->team()->create($member);

            $this->assignProjectRole($projectUser);
        }

        $project->team_lead_id = $selectedLead && $memberIds->contains((int) $selectedLead) ? $selectedLead : null;
        $project->save();

        return redirect()
            ->route('admin.project-teams.edit', $project)
            ->with('status', 'Project team updated.');
    }

    protected function assignProjectRole(ProjectUser $projectUser): void
    {
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId($projectUser->project_id);

        Role::findOrCreate($projectUser->role, 'web');
        $projectUser->user->assignRole($projectUser->role);
        $registrar->setPermissionsTeamId(0);
    }

    protected function removeProjectRole(ProjectUser $projectUser): void
    {
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId($projectUser->project_id);

        $projectUser->user->removeRole($projectUser->role);
        $registrar->setPermissionsTeamId(0);
    }
}
