@php($isEdit = isset($project))
@csrf

@if ($isEdit)
    @method('PUT')
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Title</label>
        <input
            type="text"
            name="title"
            value="{{ old('title', $project->title ?? '') }}"
            required
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Department</label>
        <input
            type="text"
            name="department"
            value="{{ old('department', $project->department ?? '') }}"
            required
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Location</label>
        <input
            type="text"
            name="location"
            value="{{ old('location', $project->location ?? '') }}"
            required
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select
            name="status"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
            @foreach (['open' => 'Open', 'closed' => 'Closed'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project->status ?? 'open') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700">Description</label>
    <textarea
        name="description"
        rows="4"
        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
    >{{ old('description', $project->description ?? '') }}</textarea>
</div>

<div class="flex justify-end gap-3">
    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded hover:bg-slate-700">
        {{ $isEdit ? 'Update Project' : 'Create Project' }}
    </button>
</div>
