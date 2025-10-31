<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Application Detail
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Review the full context for this application.
                </p>
                <a href="{{ route('applications.index') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                    Back to list
                </a>
            </div>

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

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-xl bg-white p-6 shadow space-y-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">Candidate</h2>
                            <p class="text-gray-700">
                                <a href="{{ route('candidates.show', $application->candidate) }}" class="hover:underline">
                                    {{ $application->candidate->first_name }} {{ $application->candidate->last_name }}
                                </a>
                            </p>
                            <p class="text-sm text-gray-500">{{ $application->candidate->email }}</p>
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">Project</h2>
                            <p class="text-gray-700">
                                @can('projects.view')
                                    <a href="{{ route('projects.show', $application->project) }}" class="hover:underline">
                                        {{ $application->project->title }}
                                    </a>
                                @else
                                    {{ $application->project->title }}
                                @endcan
                            </p>
                            <p class="text-sm text-gray-500">{{ $application->project->department }} • {{ $application->project->location }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-white p-6 shadow space-y-3">
                        <h2 class="text-lg font-semibold text-slate-800">Notes</h2>
                        <p class="whitespace-pre-line text-gray-600">
                            {{ $application->notes ?: 'No notes yet.' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl bg-white p-6 shadow space-y-3">
                        <h2 class="text-lg font-semibold text-slate-800">Status</h2>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700">
                            {{ ucfirst($application->status) }}
                        </span>
                        <p class="text-sm text-gray-500">
                            Last updated {{ $application->updated_at->diffForHumans() }}
                        </p>
                        @if ($application->updatedBy)
                            <p class="text-sm text-gray-500">
                                Updated by {{ $application->updatedBy->name }}
                            </p>
                        @endif
                    </div>

                    @can('applications.update')
                        <div class="rounded-xl bg-white p-6 shadow">
                            <form action="{{ route('applications.updateStatus', $application) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Update Status</label>
                                    <select name="status" class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
                                        @foreach (['applied', 'screening', 'interview', 'offer', 'hired', 'rejected'] as $status)
                                            <option value="{{ $status }}" @selected(old('status', $application->status) === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea
                                        name="notes"
                                        rows="4"
                                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                                    >{{ old('notes', $application->notes) }}</textarea>
                                </div>

                                <button type="submit"
                                        class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                                    Save Changes
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
