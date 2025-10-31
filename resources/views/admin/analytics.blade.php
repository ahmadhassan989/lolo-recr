<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Recruiting Analytics
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-8 sm:px-6 lg:px-8">
            @include('admin.partials._report_buttons')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Candidates</h3>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalCandidates }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Projects</h3>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalProjects }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Applications</h3>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalApplications }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 text-center shadow">
                    <h3 class="text-sm font-medium text-gray-500">Hired</h3>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalHired }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div class="rounded-xl bg-white p-6 shadow">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Candidates by Month</h2>
                    <canvas id="candidatesChart" height="140"></canvas>
                </div>
                <div class="rounded-xl bg-white p-6 shadow">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Interview Outcomes</h2>
                    <canvas id="interviewChart" height="140"></canvas>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Top Skills</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-2 text-left">Skill</th>
                                <th class="px-4 py-2 text-left">Candidates</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($topSkills as $skill)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ $skill['name'] }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $skill['count'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center text-gray-500">
                                        No skills detected yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.dashboardChartsData = {
            candidates: @json($monthlyCandidates),
            interviews: @json($interviewStats),
        };
    </script>
    <script src="{{ asset('js/charts/dashboard-charts.js') }}" defer></script>
</x-app-layout>
