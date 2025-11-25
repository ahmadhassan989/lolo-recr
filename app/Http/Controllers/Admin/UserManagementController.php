<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProjectLimit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display list of users for management.
     */
    public function index()
    {
        $users = User::query()
            ->with(['roles', 'projectLimit'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show edit form for a user.
     */
    public function edit(User $user)
    {
        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('admin.users.edit', [
            'user' => $user->load(['roles', 'projectLimit']),
            'roles' => $roles,
        ]);
    }

    /**
     * Update user roles.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::exists('roles', 'name')],
            'max_projects' => ['nullable', 'integer', 'min:0'],
        ]);

        $user->syncRoles([$validated['role']]);

        if (isset($validated['max_projects'])) {
            $user->projectLimit()->updateOrCreate(
                ['user_id' => $user->id],
                ['max_projects' => $validated['max_projects']]
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function create()
    {
        $this->authorize('users.create');

        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('users.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::exists('roles', 'name')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'max_projects' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$validated['role']]);

        if (isset($validated['max_projects'])) {
            $user->projectLimit()->create([
                'max_projects' => $validated['max_projects'],
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }
}
