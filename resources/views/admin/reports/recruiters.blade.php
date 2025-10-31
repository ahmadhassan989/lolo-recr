<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Recruiter Performance
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-8 sm:px-6 lg:px-8">
            <form method="GET" class="flex flex-wrap items-end gap-4 rounded-xl bg-white p-4 shadow">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1" for="from">From</label>
                    <input
                        type="date"
                        id="from"
                        name="from"
                        value="{{ $from }}"
                        class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1" for="to">To</label>
                    <input
                        type="date"
                        id="to"
                        name="to"
                        value="{{ $to }}"
                        class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="mt-4 inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        Apply
                    </button>
                    <a href="{{ route('admin.reports.recruiters') }}" class="mt-4 text-sm text-gray-600 hover:text-gray-800">
                        Reset
                    </a>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('admin.reports.recruiters.export', request()->only(['from', 'to'])) }}"
                       class="mt-4 inline-flex items-center rounded bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-600">
                        ⬇️ Export CSV
                    </a>
                </div>
            </form>

            <div class="rounded-xl bg-white p-6 shadow">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">Hires & Conversion</h3>
                <canvas id="recruiterChart" height="160"></canvas>
            </div>

            <div class="overflow-x-auto rounded-xl bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Recruiter</th>
                            <th class="px-6 py-3 text-left">Candidates Added</th>
                            <th class="px-6 py-3 text-left">Interviews</th>
                            <th class="px-6 py-3 text-left">Hired</th>
                            <th class="px-6 py-3 text-left">Conversion %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($performance as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $row['name'] }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $row['added'] }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $row['interviews'] }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $row['hired'] }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $row['conversion'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recruiter activity for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.recruiterPerformance = {
            labels: @json($performance->pluck('name')),
            hires: @json($performance->pluck('hired')),
            conversions: @json($performance->pluck('conversion')),
        };
    </script>
    <script src="{{ asset('js/charts/recruiter-performance.js') }}" defer></script>
</x-app-layout>
