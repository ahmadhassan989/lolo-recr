<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <p class="text-sm text-gray-600">
                High-level overview of recruiting activity across the organization.
            </p>

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
