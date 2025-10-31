<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Job Posts
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
                    Manage open roles and track draft postings across teams.
                </p>
                @can('jobs.manage')
                    <a href="{{ route('jobs.create') }}"
                       class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700">
                        New Job Post
                    </a>
                @endcan
            </div>

            <div class="overflow-x-auto rounded-xl bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Project</th>
                            <th class="px-6 py-3">Employment</th>
                            <th class="px-6 py-3">Deadline</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($jobs as $job)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <a href="{{ route('jobs.show', $job) }}" class="hover:underline">
                                        {{ $job->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $job->department ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $job->project->title ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ optional($job->deadline)->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize
                                        @class([
                                            'bg-green-100 text-green-700' => $job->status === 'open',
                                            'bg-amber-100 text-amber-700' => $job->status === 'draft',
                                            'bg-gray-200 text-gray-700' => $job->status === 'closed',
                                        ])">
                                        {{ $job->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('jobs.show', $job) }}" class="text-slate-700 hover:text-slate-900">View</a>
                                    @can('jobs.manage')
                                        <a href="{{ route('jobs.edit', $job) }}" class="text-slate-700 hover:text-slate-900">Edit</a>
                                        <form action="{{ route('jobs.destroy', $job) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-rose-600 hover:text-rose-800"
                                                    onclick="return confirm('Delete this job post?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No job posts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $jobs->links() }}
        </div>
    </div>
</x-app-layout>
