<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Create Job Offer
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-md bg-red-100 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-xl bg-white p-6 shadow">
                <h1 class="text-2xl font-semibold text-slate-800 mb-4">
                    {{ $candidate->first_name }} {{ $candidate->last_name }}
                </h1>

                <form action="{{ route('job-offers.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Project</label>
                        <select
                            name="project_id"
                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                        >
                            <option value="">Select project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Position Title</label>
                        <input
                            type="text"
                            name="position_title"
                            value="{{ old('position_title') }}"
                            required
                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                        >
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Salary</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="salary"
                                value="{{ old('salary') }}"
                                class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                            >
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Currency</label>
                            <input
                                type="text"
                                name="currency"
                                value="{{ old('currency', 'USD') }}"
                                class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                            >
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input
                                type="date"
                                name="start_date"
                                value="{{ old('start_date') }}"
                                class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                            >
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Contract Duration</label>
                            <input
                                type="text"
                                name="contract_duration"
                                value="{{ old('contract_duration') }}"
                                class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                            >
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            name="notes"
                            rows="4"
                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                        >{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('candidates.show', $candidate) }}" class="inline-flex items-center rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">
                            Create Offer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
