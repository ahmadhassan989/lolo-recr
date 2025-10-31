@php($isEdit = isset($candidate))
@csrf

@if ($isEdit)
    @method('PUT')
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">First Name</label>
        <input
            type="text"
            name="first_name"
            value="{{ old('first_name', $candidate->first_name ?? '') }}"
            required
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Last Name</label>
        <input
            type="text"
            name="last_name"
            value="{{ old('last_name', $candidate->last_name ?? '') }}"
            required
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input
            type="email"
            name="email"
            value="{{ old('email', $candidate->email ?? '') }}"
            required
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input
            type="text"
            name="phone"
            value="{{ old('phone', $candidate->phone ?? '') }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">CV File</label>
        <input
            type="text"
            name="cv_file"
            value="{{ old('cv_file', $candidate->cv_file ?? '') }}"
            placeholder="storage/cv/jane-doe.pdf"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
        <input
            type="url"
            name="linkedin_url"
            value="{{ old('linkedin_url', $candidate->linkedin_url ?? '') }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Gender</label>
        <select
            name="gender"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
            <option value="">Select gender</option>
            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', $candidate->gender ?? '') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Birth Date</label>
        <input
            type="date"
            name="birth_date"
            value="{{ old('birth_date', optional(optional($candidate ?? null)->birth_date)->format('Y-m-d')) }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Nationality</label>
        <input
            type="text"
            name="nationality"
            value="{{ old('nationality', $candidate->nationality ?? '') }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Education Level</label>
        <input
            type="text"
            name="education_level"
            value="{{ old('education_level', $candidate->education_level ?? '') }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Expected Salary</label>
        <input
            type="number"
            name="expected_salary"
            value="{{ old('expected_salary', isset($candidate) && $candidate->expected_salary !== null ? number_format($candidate->expected_salary, 2, '.', '') : '') }}"
            step="0.01"
            min="0"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Availability Date</label>
        <input
            type="date"
            name="availability_date"
            value="{{ old('availability_date', optional(optional($candidate ?? null)->availability_date)->format('Y-m-d')) }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Source</label>
        <input
            type="text"
            name="source"
            value="{{ old('source', $candidate->source ?? '') }}"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Rating (0-5)</label>
        <input
            type="number"
            name="rating"
            value="{{ old('rating', $candidate->rating ?? 0) }}"
            min="0"
            max="5"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select
            name="status"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
            @foreach (['active' => 'Active', 'archived' => 'Archived', 'blacklisted' => 'Blacklisted'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $candidate->status ?? 'active') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">Experience (years)</label>
        <input
            type="number"
            name="experience_years"
            value="{{ old('experience_years', $candidate->experience_years ?? 0) }}"
            min="0"
            class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
        >
    </div>
</div>

<div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700">Skills</label>
    <textarea
        name="skills"
        rows="3"
        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
    >{{ old('skills', $candidate->skills ?? '') }}</textarea>
</div>

<div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700">Notes</label>
    <textarea
        name="notes"
        rows="4"
        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
    >{{ old('notes', $candidate->notes ?? '') }}</textarea>
</div>

<div class="flex justify-end gap-3">
    <a href="{{ route('candidates.index') }}" class="inline-flex items-center rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
    <button type="submit" class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">
        {{ $isEdit ? 'Update Candidate' : 'Create Candidate' }}
    </button>
</div>
