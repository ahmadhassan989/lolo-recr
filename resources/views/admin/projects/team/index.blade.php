<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Project Teams
                </h2>
                <p class="text-sm text-gray-500">Assign HRs and recruiters to each project.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Project</th>
                            <th class="px-4 py-3">Team Members</th>
                            <th class="px-4 py-3">Team Lead</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($projects as $project)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $project->title }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    @forelse ($project->team as $member)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 mr-2 mb-2">
                                            {{ $member->user->name }} – {{ ucfirst($member->role) }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 text-sm">No team assigned</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    @if ($project->teamLead)
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-slate-800">{{ $project->teamLead->name }}</span>
                                            <span class="text-xs text-slate-500">{{ $project->teamLead->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-sm">Not set</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.project-teams.edit', $project) }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                                        Manage Team
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $projects->links() }}
        </div>
    </div>
</x-app-layout>
