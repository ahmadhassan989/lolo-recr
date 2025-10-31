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

            <div class="rounded-xl bg-white p-6 shadow">
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
