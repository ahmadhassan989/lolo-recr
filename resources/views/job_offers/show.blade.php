<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Job Offer Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-xl bg-white p-6 shadow space-y-4">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-800">{{ $offer->position_title }}</h1>
                    <p class="text-sm text-gray-500">Candidate: {{ $offer->candidate->first_name }} {{ $offer->candidate->last_name }}</p>
                </div>

                <dl class="grid gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Project</dt>
                        <dd class="text-gray-700">{{ $offer->project->title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize
                                @class([
                                    'bg-amber-100 text-amber-700' => $offer->status === 'pending',
                                    'bg-green-100 text-green-700' => $offer->status === 'accepted',
                                    'bg-rose-100 text-rose-700' => $offer->status === 'declined',
                                    'bg-gray-200 text-gray-700' => $offer->status === 'expired',
                                ])">
                                {{ $offer->status }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Salary</dt>
                        <dd class="text-gray-700">
                            {{ $offer->salary ? number_format($offer->salary, 2) : '—' }} {{ $offer->currency }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Start Date</dt>
                        <dd class="text-gray-700">{{ optional($offer->start_date)->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Contract Duration</dt>
                        <dd class="text-gray-700">{{ $offer->contract_duration ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Created By</dt>
                        <dd class="text-gray-700">{{ $offer->creator->name ?? '—' }}</dd>
                    </div>
                </dl>

                <div class="space-y-2">
                    <dt class="text-xs font-semibold uppercase text-gray-500">Notes</dt>
                    <dd class="whitespace-pre-line text-gray-700">{{ $offer->notes ?: '—' }}</dd>
                </div>

                <div class="flex flex-wrap gap-3 pt-4">
                    <a href="{{ route('job-offers.export', $offer) }}" class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        ⬇️ Export PDF
                    </a>
                    @can('offers.manage')
                        <form action="{{ route('job-offers.status', $offer) }}" method="POST" class="inline-flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <select name="status" class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
                                @foreach (['pending', 'accepted', 'declined', 'expired'] as $status)
                                    <option value="{{ $status }}" @selected($offer->status === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="inline-flex items-center rounded bg-slate-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-600">
                                Update Status
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
