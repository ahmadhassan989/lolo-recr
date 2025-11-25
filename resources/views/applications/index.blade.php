<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Applications
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-xl bg-white p-6 shadow space-y-4">
                <form method="GET" class="grid gap-4 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search Candidate</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500" placeholder="Name or email">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach (['applied','screening','interview','offer','hired','rejected'] as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Project</label>
                        <select name="project_id" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach ($projectOptions as $projectOption)
                                <option value="{{ $projectOption->id }}" @selected(($filters['project_id'] ?? '') == $projectOption->id)>{{ $projectOption->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">From</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">To</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                        </div>
                    </div>
                    <div class="md:col-span-5 flex justify-end gap-3">
                        <a href="{{ route('applications.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
                        <button class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Apply</button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Project</th>
                                <th class="px-6 py-3">Candidate</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Updated</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($applications as $application)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-700">
                                        @can('projects.view')
                                            <a href="{{ route('projects.show', $application->project) }}" class="hover:underline">
                                                {{ $application->project->title }}
                                            </a>
                                        @else
                                            {{ $application->project->title }}
                                        @endcan
                                    </td>
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
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No applications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
