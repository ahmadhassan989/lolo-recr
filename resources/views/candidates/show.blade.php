<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $candidate->first_name }} {{ $candidate->last_name }}
            </h2>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                @class([
                    'bg-green-100 text-green-700' => $candidate->status === 'active',
                    'bg-amber-100 text-amber-700' => $candidate->status === 'archived',
                    'bg-rose-100 text-rose-700' => $candidate->status === 'blacklisted',
                ])">
                {{ ucfirst($candidate->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-md bg-red-100 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-1 text-gray-500">
                    <p>
                        {{ $candidate->email }}
                        @if ($candidate->phone)
                            • {{ $candidate->phone }}
                        @endif
                    </p>
                    <p class="text-sm">
                        Added {{ $candidate->created_at->format('M d, Y') }} • Last updated {{ $candidate->updated_at->diffForHumans() }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @can('candidates.manage')
                        <a href="{{ route('candidates.edit', $candidate) }}"
                           class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                            Edit Candidate
                        </a>
                        <form action="{{ route('candidates.destroy', $candidate) }}" method="POST" class="inline-flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="rounded-md border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2"
                                    onclick="return confirm('Delete this candidate?')">
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div>
                    @include('candidates.partials._rating')
                </div>
                <div class="lg:col-span-2">
                    @include('candidates.partials._tags')
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800">Job Offers</h3>
                    @can('offers.manage')
                        <a href="{{ route('job-offers.create', ['candidate_id' => $candidate->id]) }}"
                           class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            New Job Offer
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-2 text-left">Position</th>
                                <th class="px-4 py-2 text-left">Project</th>
                                <th class="px-4 py-2 text-left">Salary</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($candidate->jobOffers as $offer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-slate-800">{{ $offer->position_title }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $offer->project->title ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $offer->salary ? number_format($offer->salary, 2) : '—' }} {{ $offer->currency }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize
                                            @class([
                                                'bg-amber-100 text-amber-700' => $offer->status === 'pending',
                                                'bg-green-100 text-green-700' => $offer->status === 'accepted',
                                                'bg-rose-100 text-rose-700' => $offer->status === 'declined',
                                                'bg-gray-200 text-gray-700' => $offer->status === 'expired',
                                            ])">
                                            {{ $offer->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('job-offers.show', $offer) }}" class="text-slate-700 hover:text-slate-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                        No offers yet for this candidate.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl bg-white p-6 shadow space-y-4">
                        <h2 class="text-lg font-semibold text-slate-800">Skills</h2>
                        <p class="whitespace-pre-line text-gray-600">
                            {{ $candidate->skills ?: 'No skills listed.' }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-white p-6 shadow space-y-4">
                        <h2 class="text-lg font-semibold text-slate-800">Notes</h2>
                        <p class="whitespace-pre-line text-gray-600">
                            {{ $candidate->notes ?: 'No notes available.' }}
                        </p>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow space-y-3">
                    <h2 class="text-lg font-semibold text-slate-800">Profile</h2>
                    <p><span class="font-medium text-gray-700">Experience:</span> {{ $candidate->experience_years }} years</p>
                    <p><span class="font-medium text-gray-700">Gender:</span> {{ ucfirst($candidate->gender ?? '—') }}</p>
                    <p><span class="font-medium text-gray-700">Birth Date:</span> {{ optional($candidate->birth_date)->format('M d, Y') ?? '—' }}</p>
                    <p><span class="font-medium text-gray-700">Nationality:</span> {{ $candidate->nationality ?? '—' }}</p>
                    <p><span class="font-medium text-gray-700">Education:</span> {{ $candidate->education_level ?? '—' }}</p>
                    <p><span class="font-medium text-gray-700">Expected Salary:</span>
                        {{ $candidate->expected_salary !== null ? number_format($candidate->expected_salary, 2) : '—' }}
                    </p>
                    <p><span class="font-medium text-gray-700">Available From:</span>
                        {{ optional($candidate->availability_date)->format('M d, Y') ?? '—' }}
                    </p>
                    <p><span class="font-medium text-gray-700">Source:</span> {{ $candidate->source ?? '—' }}</p>
                    @if ($candidate->linkedin_url)
                        <p><span class="font-medium text-gray-700">LinkedIn:</span>
                            <a href="{{ $candidate->linkedin_url }}" target="_blank" rel="noopener" class="text-slate-700 hover:underline">
                                View Profile
                            </a>
                        </p>
                    @endif
                    @if ($candidate->cv_file)
                        <p><span class="font-medium text-gray-700">CV:</span>
                            <a href="{{ asset($candidate->cv_file) }}" target="_blank" rel="noopener" class="text-slate-700 hover:underline">
                                Download
                            </a>
                        </p>
                    @endif
                    <p><span class="font-medium text-gray-700">Added:</span> {{ $candidate->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            @include('candidates.partials._activity_log')

            <div x-data="{ tab: 'interviews' }" class="rounded-xl bg-white p-6 shadow space-y-6">
                <div class="flex gap-4 border-b border-gray-200 pb-2">
                    <button
                        type="button"
                        class="px-3 py-2 text-sm font-semibold transition"
                        :class="tab === 'interviews' ? 'border-b-2 border-slate-800 text-slate-800' : 'text-gray-500'"
                        @click="tab = 'interviews'"
                    >
                        Interviews
                    </button>
                    <button
                        type="button"
                        class="px-3 py-2 text-sm font-semibold transition"
                        :class="tab === 'attachments' ? 'border-b-2 border-slate-800 text-slate-800' : 'text-gray-500'"
                        @click="tab = 'attachments'"
                    >
                        Attachments
                    </button>
                </div>

                <div class="space-y-6">
                    @include('candidates.partials._interviews')
                    @include('candidates.partials._attachments')
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Applications</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left">Project</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Updated</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($candidate->applications as $application)
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
