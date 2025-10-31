<div x-show="tab === 'interviews'" x-cloak class="space-y-6">
    @can('candidates.manage')
        <div>
            <h3 class="text-sm font-semibold text-slate-700 mb-3">Log Interview</h3>
            <form action="{{ route('interviews.store', $candidate) }}" method="POST" class="grid gap-4 lg:grid-cols-2">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Project</label>
                    <select
                        name="project_id"
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                        <option value="">Unassigned</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Interview Date</label>
                    <input
                        type="datetime-local"
                        name="interview_date"
                        value="{{ old('interview_date') }}"
                        required
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Type</label>
                    <select
                        name="type"
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                        @foreach (['hr' => 'HR Screen', 'technical' => 'Technical', 'final' => 'Final'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', 'hr') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Result</label>
                    <select
                        name="result"
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                        @foreach (['pending' => 'Pending', 'passed' => 'Passed', 'failed' => 'Failed'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('result', 'pending') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Interviewer</label>
                    <input
                        type="text"
                        name="interviewer"
                        value="{{ old('interviewer') }}"
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                </div>
                <div class="space-y-1 lg:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Notes</label>
                    <textarea
                        name="notes"
                        rows="3"
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >{{ old('notes') }}</textarea>
                </div>
                <div class="lg:col-span-2 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                    >
                        Save Interview
                    </button>
                </div>
            </form>
        </div>
    @endcan

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Project</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-left">Result</th>
                    <th class="px-4 py-2 text-left">Interviewer</th>
                    <th class="px-4 py-2 text-left">Notes</th>
                    @can('candidates.manage')
                        <th class="px-4 py-2 text-right">Actions</th>
                    @endcan
                </tr>
            </thead>
            <tbody x-data="{ editing: null }" class="divide-y divide-gray-200">
                @forelse ($candidate->interviews as $interview)
                    <tr class="align-top">
                        <td class="px-4 py-3 text-gray-700">
                            {{ $interview->interview_date?->format('M d, Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $interview->project?->title ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst($interview->type) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                @class([
                                    'bg-amber-100 text-amber-700' => $interview->result === 'pending',
                                    'bg-green-100 text-green-700' => $interview->result === 'passed',
                                    'bg-rose-100 text-rose-700' => $interview->result === 'failed',
                                ])">
                                {{ ucfirst($interview->result) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $interview->interviewer ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $interview->notes ?? '—' }}</td>
                        @can('candidates.manage')
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <button
                                        type="button"
                                        class="text-slate-700 hover:text-slate-900"
                                        @click="editing = editing === {{ $interview->id }} ? null : {{ $interview->id }}"
                                    >
                                        Edit
                                    </button>
                                    <form action="{{ route('interviews.destroy', $interview) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800" onclick="return confirm('Remove this interview?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endcan
                    </tr>
                    @can('candidates.manage')
                        <tr x-show="editing === {{ $interview->id }}" x-cloak>
                            <td colspan="7" class="px-4 pb-4">
                                <form action="{{ route('interviews.update', $interview) }}" method="POST" class="grid gap-4 lg:grid-cols-2 bg-slate-50 p-4 rounded">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-1">
                                        <label class="block text-xs font-medium text-gray-600 uppercase">Project</label>
                                        <select
                                            name="project_id"
                                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                                        >
                                            <option value="">Unassigned</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}" @selected(old('project_id', $interview->project_id) == $project->id)>
                                                    {{ $project->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-xs font-medium text-gray-600 uppercase">Interview Date</label>
                                        <input
                                            type="datetime-local"
                                            name="interview_date"
                                            value="{{ old('interview_date', $interview->interview_date?->format('Y-m-d\TH:i')) }}"
                                            required
                                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                                        >
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-xs font-medium text-gray-600 uppercase">Type</label>
                                        <select
                                            name="type"
                                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                                        >
                                            @foreach (['hr' => 'HR Screen', 'technical' => 'Technical', 'final' => 'Final'] as $value => $label)
                                                <option value="{{ $value }}" @selected(old('type', $interview->type) === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-xs font-medium text-gray-600 uppercase">Result</label>
                                        <select
                                            name="result"
                                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                                        >
                                            @foreach (['pending' => 'Pending', 'passed' => 'Passed', 'failed' => 'Failed'] as $value => $label)
                                                <option value="{{ $value }}" @selected(old('result', $interview->result) === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-xs font-medium text-gray-600 uppercase">Interviewer</label>
                                        <input
                                            type="text"
                                            name="interviewer"
                                            value="{{ old('interviewer', $interview->interviewer) }}"
                                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                                        >
                                    </div>
                                    <div class="space-y-1 lg:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 uppercase">Notes</label>
                                        <textarea
                                            name="notes"
                                            rows="3"
                                            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                                        >{{ old('notes', $interview->notes) }}</textarea>
                                    </div>
                                    <div class="lg:col-span-2 flex justify-end gap-3">
                                        <button type="button" class="text-gray-600 hover:text-gray-800" @click.prevent="editing = null">
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                                        >
                                            Update Interview
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endcan
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('candidates.manage') ? 7 : 6 }}" class="px-4 py-3 text-center text-gray-500">
                            No interviews recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
