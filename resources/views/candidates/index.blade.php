<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Candidates
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-sm text-gray-600">
                    Use the smart filters to quickly find the right candidates.
                </p>
                @can('candidates.manage')
                    <a href="{{ route('candidates.create') }}"
                       class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                        New Candidate
                    </a>
                @endcan
            </div>

            <form method="GET" class="rounded-xl bg-white p-4 shadow">
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-2 space-y-1">
                        <label class="block text-xs font-medium text-gray-600 uppercase">Search</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Name, email, or skill"
                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-gray-600 uppercase">Status</label>
                        <select
                            name="status"
                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">All statuses</option>
                            @foreach (['active' => 'Active', 'archived' => 'Archived', 'blacklisted' => 'Blacklisted'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-gray-600 uppercase">Min. Experience</label>
                        <input
                            type="number"
                            name="min_exp"
                            value="{{ request('min_exp') }}"
                            min="0"
                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                        >
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-gray-600 uppercase">Assigned Project</label>
                        <select name="project_id" class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">Any</option>
                            @foreach ($filterProjects as $filterProject)
                                <option value="{{ $filterProject->id }}" @selected(request('project_id') == $filterProject->id)>{{ $filterProject->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        Apply Filters
                    </button>
                    @if (request()->filled('search') || request()->filled('status') || request()->filled('min_exp'))
                        <a href="{{ route('candidates.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="overflow-x-auto rounded-xl bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Experience</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Rating</th>
                            <th class="px-6 py-3">Tags</th>
                            <th class="px-6 py-3">Applications</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($candidates as $candidate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <a href="{{ route('candidates.show', $candidate) }}" class="hover:underline">
                                        {{ $candidate->first_name }} {{ $candidate->last_name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $candidate->email }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $candidate->experience_years }} yrs</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                        @class([
                                            'bg-green-100 text-green-700' => $candidate->status === 'active',
                                            'bg-amber-100 text-amber-700' => $candidate->status === 'archived',
                                            'bg-rose-100 text-rose-700' => $candidate->status === 'blacklisted',
                                        ])">
                                        {{ ucfirst($candidate->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    @if ($candidate->rating)
                                        <span class="font-semibold text-slate-800">{{ $candidate->rating }}</span><span class="text-xs text-amber-500">★</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    @if ($candidate->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($candidate->tags->take(3) as $tag)
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                            @if ($candidate->tags->count() > 3)
                                                <span class="text-xs text-gray-500">
                                                    +{{ $candidate->tags->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $candidate->applications_count }}</td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('candidates.show', $candidate) }}" class="text-slate-700 hover:text-slate-900">
                                        View
                                    </a>
                                    @can('candidates.manage')
                                        <a href="{{ route('candidates.edit', $candidate) }}" class="text-slate-700 hover:text-slate-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('candidates.destroy', $candidate) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-rose-600 hover:text-rose-800"
                                                    onclick="return confirm('Delete this candidate?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No candidates match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $candidates->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
