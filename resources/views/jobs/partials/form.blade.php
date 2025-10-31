@php($job = $job ?? null)

<div class="grid gap-4 md:grid-cols-2">
    <div class="space-y-1 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Title</label>
        <input
            type="text"
            name="title"
            value="{{ old('title', $job->title ?? '') }}"
            required
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Department</label>
        <input
            type="text"
            name="department"
            value="{{ old('department', $job->department ?? '') }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Project</label>
        <select
            name="project_id"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
            <option value="">No project linked</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected(old('project_id', $job->project_id ?? null) == $project->id)>
                    {{ $project->title }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="space-y-1">
    <label class="block text-sm font-medium text-gray-700">Location</label>
    <input
        type="text"
        name="location"
        value="{{ old('location', $job->location ?? '') }}"
        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
    >
</div>

<div class="space-y-1">
    <label class="block text-sm font-medium text-gray-700">Description</label>
    <textarea
        name="description"
        rows="4"
        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
    >{{ old('description', $job->description ?? '') }}</textarea>
</div>

<div class="space-y-1">
    <label class="block text-sm font-medium text-gray-700">Requirements</label>
    <textarea
        name="requirements"
        rows="4"
        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
    >{{ old('requirements', $job->requirements ?? '') }}</textarea>
</div>

<div class="space-y-1">
    <label class="block text-sm font-medium text-gray-700">Skills</label>
    <textarea
        name="skills"
        rows="3"
        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
    >{{ old('skills', $job->skills ?? '') }}</textarea>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Employment Type</label>
        <select
            name="employment_type"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
            @foreach ([
                'full_time' => 'Full Time',
                'part_time' => 'Part Time',
                'contract' => 'Contract',
                'internship' => 'Internship',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('employment_type', $job->employment_type ?? 'full_time') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Salary Range</label>
        <input
            type="text"
            name="salary_range"
            value="{{ old('salary_range', $job->salary_range ?? '') }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Deadline</label>
        <input
            type="date"
            name="deadline"
            value="{{ old('deadline', optional($job->deadline ?? null)->format('Y-m-d')) }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>
    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select
            name="status"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
            @foreach (['open' => 'Open', 'draft' => 'Draft', 'closed' => 'Closed'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $job->status ?? 'open') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>
