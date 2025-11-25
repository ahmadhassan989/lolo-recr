<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-8 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-900 text-2xl font-semibold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-2xl font-semibold text-slate-900">{{ $user->name }}</div>
                            <div class="text-sm text-slate-500">{{ $user->email }}</div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @forelse($user->getRoleNames() as $role)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400">No role assigned</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="text-sm text-slate-500">
                        <p class="font-medium text-slate-800">Member since</p>
                        <p>{{ optional($user->created_at)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <x-profile-stat-card label="Assigned Projects" :value="$stats['assigned_projects']" />
                <x-profile-stat-card label="Leading Projects" :value="$stats['leading_projects']" />
                <x-profile-stat-card label="Applications Updated" :value="$stats['applications_updated']" />
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    @include('profile.partials.update-profile-information-form')
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm">
                @include('profile.partials.delete-user-form')
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-800">Projects You're On</h3>
                    <p class="text-sm text-slate-500 mb-4">Projects where you collaborate as HR or recruiter.</p>
                    @if ($assignedProjects->isEmpty())
                        <p class="text-sm text-slate-500">You're not assigned to any projects yet.</p>
                    @else
                        <ul class="divide-y divide-slate-100">
                            @foreach ($assignedProjects as $project)
                                <li class="py-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $project->title }}</p>
                                            <p class="text-xs text-slate-500">{{ ucfirst($project->pivot->role ?? 'member') }} • {{ $project->applications_count }} applications</p>
                                        </div>
                                        <a href="{{ route('projects.show', $project) }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                                            View
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-800">Projects You Lead</h3>
                    <p class="text-sm text-slate-500 mb-4">You are designated as the team lead on these projects.</p>
                    @if ($teamLeadProjects->isEmpty())
                        <p class="text-sm text-slate-500">You're not leading any projects at the moment.</p>
                    @else
                        <ul class="divide-y divide-slate-100">
                            @foreach ($teamLeadProjects as $project)
                                <li class="py-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $project->title }}</p>
                                            <p class="text-xs text-slate-500 uppercase">{{ $project->status }} • {{ $project->applications_count }} applications</p>
                                        </div>
                                        <a href="{{ route('projects.show', $project) }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                                            View
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
