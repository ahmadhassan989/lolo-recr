<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-4 shadow">
                <form method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">From</label>
                        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">To</label>
                        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
                        <button class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Update</button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Candidates</h3>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['candidates'] }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Open Projects</h3>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['open_projects'] }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Applications</h3>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['applications'] }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Hired</h3>
                    <p class="mt-2 text-2xl font-bold text-slate-800">{{ $stats['hired'] }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
