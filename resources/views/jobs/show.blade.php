<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            {{ $job->title }}
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
                <dl class="grid gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Department</dt>
                        <dd class="text-gray-700">{{ $job->department ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Project</dt>
                        <dd class="text-gray-700">{{ $job->project->title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Location</dt>
                        <dd class="text-gray-700">{{ $job->location ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Employment Type</dt>
                        <dd class="text-gray-700">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Salary Range</dt>
                        <dd class="text-gray-700">{{ $job->salary_range ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Deadline</dt>
                        <dd class="text-gray-700">{{ optional($job->deadline)->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Status</dt>
                        <dd class="text-gray-700 capitalize">{{ $job->status }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Created By</dt>
                        <dd class="text-gray-700">{{ $job->creator->name ?? '—' }}</dd>
                    </div>
                </dl>

                <div class="space-y-2">
                    <dt class="text-xs font-semibold uppercase text-gray-500">Description</dt>
                    <dd class="whitespace-pre-line text-gray-700">{{ $job->description ?: '—' }}</dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-xs font-semibold uppercase text-gray-500">Requirements</dt>
                    <dd class="whitespace-pre-line text-gray-700">{{ $job->requirements ?: '—' }}</dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-xs font-semibold uppercase text-gray-500">Skills</dt>
                    <dd class="whitespace-pre-line text-gray-700">{{ $job->skills ?: '—' }}</dd>
                </div>

                @can('jobs.manage')
                    <div class="flex gap-3 pt-4">
                        <a href="{{ route('jobs.edit', $job) }}" class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Edit Job
                        </a>
                        <form action="{{ route('jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Delete this job post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center rounded border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50">
                                Delete
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
