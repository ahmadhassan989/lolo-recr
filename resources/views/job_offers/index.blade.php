<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Job Offers
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-xl bg-white p-4 shadow space-y-4">
                <form method="GET" class="grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Search Candidate</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500" placeholder="Name or email">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Status</label>
                        <select name="status" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach (['pending','accepted','declined','expired'] as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Project</label>
                        <select name="project_id" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">All</option>
                            @foreach ($projectOptions as $project)
                                <option value="{{ $project->id }}" @selected(($filters['project_id'] ?? '') == $project->id)>{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4 flex justify-end gap-3">
                        <a href="{{ route('job-offers.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
                        <button class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Apply</button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Candidate</th>
                            <th class="px-6 py-3 text-left">Project</th>
                            <th class="px-6 py-3 text-left">Position</th>
                            <th class="px-6 py-3 text-left">Salary</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Created By</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($offers as $offer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-slate-800">
                                    {{ $offer->candidate->first_name }} {{ $offer->candidate->last_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $offer->project->title ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $offer->position_title }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $offer->salary ? number_format($offer->salary, 2) : '—' }} {{ $offer->currency }}
                                </td>
                                <td class="px-6 py-4">
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
                                <td class="px-6 py-4 text-gray-600">{{ $offer->creator->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('job-offers.show', $offer) }}" class="text-slate-700 hover:text-slate-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No job offers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $offers->links() }}
        </div>
    </div>
</x-app-layout>
