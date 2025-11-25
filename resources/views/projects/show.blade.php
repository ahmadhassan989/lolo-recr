<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $project->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-gray-500">
                        {{ $project->department }} • {{ $project->location }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @can('projects.manage')
                        <a href="{{ route('projects.edit', $project) }}"
                           class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                            Edit Project
                        </a>
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="rounded-md border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2"
                                    onclick="return confirm('Delete this project?')">
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl bg-white p-6 shadow space-y-4">
                        <h2 class="text-lg font-semibold text-slate-800">Description</h2>
                        <p class="whitespace-pre-line text-gray-600">
                            {{ $project->description ?: 'No description provided.' }}
                        </p>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow space-y-3">
                    <h2 class="text-lg font-semibold text-slate-800">Details</h2>
                    <p><span class="font-medium text-gray-700">Status:</span>
                        <span class="ml-2 inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $project->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </p>
                    <p><span class="font-medium text-gray-700">Created:</span> {{ $project->created_at->format('M d, Y') }}</p>
                    <p><span class="font-medium text-gray-700">Applications:</span> {{ $project->applications->count() }}</p>
                    <div>
                        <span class="font-medium text-gray-700">Team Lead:</span>
                        @if ($project->teamLead)
                            <div class="mt-1 text-sm text-gray-700">
                                <div class="font-semibold text-slate-800">{{ $project->teamLead->name }}</div>
                                <div class="text-xs text-slate-500">{{ $project->teamLead->email }}</div>
                            </div>
                        @else
                            <span class="ml-2 text-sm text-slate-500">Not assigned</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Applications</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left">Candidate</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Updated</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($project->applications as $application)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-700">
                                        @can('candidates.view')
                                            <a href="{{ route('candidates.show', $application->candidate) }}" class="hover:underline">
                                                {{ $application->candidate->first_name }} {{ $application->candidate->last_name }}
                                            </a>
                                        @else
                                            {{ $application->candidate->first_name }} {{ $application->candidate->last_name }}
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $application->updated_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('applications.show', $application) }}" class="text-slate-700 hover:text-slate-900">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No applications yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
