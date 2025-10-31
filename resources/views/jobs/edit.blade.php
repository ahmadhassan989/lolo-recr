<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Edit Job Post
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
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
                <form action="{{ route('jobs.update', $job) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @include('jobs.partials.form', ['job' => $job, 'projects' => $projects])

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-white hover:bg-slate-700">
                            Update Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
