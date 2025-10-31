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

            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Track open roles and hiring initiatives across the organization.
                </p>
                @can('projects.manage')
                    <a href="{{ route('projects.create') }}"
                       class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                        New Project
                    </a>
                @endcan
            </div>

            <div class="overflow-x-auto rounded-xl bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Location</th>
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
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
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
