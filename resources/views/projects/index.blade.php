<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Projects
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">
                    Track open roles and hiring initiatives across the organization.
                </p>
                @can('create', App\Models\Project::class)
                    <a href="{{ route('projects.create') }}"
                       class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                        New Project
                    </a>
                @endcan
            </div>

            <div class="rounded-xl bg-white p-4 shadow">
                <form method="GET" class="grid gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500" placeholder="Title or department">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach (['open' => 'Open', 'closed' => 'Closed'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Department</label>
                        <select name="department" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}" @selected(($filters['department'] ?? '') === $department)>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Team Lead</label>
                        <select name="team_lead_id" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach ($teamLeads as $lead)
                                <option value="{{ $lead->id }}" @selected(($filters['team_lead_id'] ?? '') == $lead->id)>{{ $lead->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4 flex gap-3 justify-end">
                        <a href="{{ route('projects.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
                        <button class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Apply</button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Location</th>
                            <th class="px-6 py-3">Team Lead</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Applications</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <a href="{{ route('projects.show', $project) }}" class="hover:underline">
                                        {{ $project->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $project->department }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $project->location }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    @if ($project->teamLead)
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-800">{{ $project->teamLead->name }}</span>
                                            <span class="text-xs text-slate-500">{{ $project->teamLead->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $project->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $project->applications_count }}</td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('projects.show', $project) }}" class="text-slate-700 hover:text-slate-900">
                                        View
                                    </a>
                                    @can('projects.manage')
                                        <a href="{{ route('projects.edit', $project) }}" class="text-slate-700 hover:text-slate-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-rose-600 hover:text-rose-800"
                                                    onclick="return confirm('Delete this project?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
