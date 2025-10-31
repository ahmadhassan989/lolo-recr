<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class AnalyticsController extends Controller
{
    /**
     * Display recruiting analytics.
     */
    public function __invoke(): View
    {
        $totalCandidates = Candidate::count();
        $totalProjects = Project::count();
        $totalApplications = Application::count();
        $totalHired = Application::where('status', 'hired')->count();

        $monthlyCandidates = Candidate::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $monthlyChart = $this->formatMonthlyChart($monthlyCandidates);

        $topSkills = $this->topSkills();

        $interviewStats = Interview::query()
            ->selectRaw('result, COUNT(*) as total')
            ->groupBy('result')
            ->pluck('total', 'result');

        return view('admin.analytics', [
            'totalCandidates' => $totalCandidates,
            'totalProjects' => $totalProjects,
            'totalApplications' => $totalApplications,
            'totalHired' => $totalHired,
            'monthlyCandidates' => $monthlyChart,
            'topSkills' => $topSkills,
            'interviewStats' => $interviewStats->toArray(),
        ]);
    }

    /**
     * Format monthly recruitment chart data.
     *
     * @param  Collection<string, int>  $raw
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    protected function formatMonthlyChart(Collection $raw): array
    {
        $period = collect();
        $cursor = now()->subMonths(11)->startOfMonth();
        $end = now()->startOfMonth();

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');
            $period->put($key, [
                'label' => $cursor->format('M Y'),
                'value' => (int) ($raw[$key] ?? 0),
            ]);

            $cursor->addMonth();
        }

        return [
            'labels' => $period->pluck('label')->toArray(),
            'data' => $period->pluck('value')->toArray(),
        ];
    }

    /**
     * Determine the top N skills occurring in candidate profiles.
     *
     * @return array<int, array{name: string, count: int}>
     */
    protected function topSkills(int $limit = 5): array
    {
        $skills = Candidate::query()
            ->whereNotNull('skills')
            ->pluck('skills')
            ->flatMap(function (string $skillLine) {
                return collect(explode(',', $skillLine))
                    ->map(fn ($skill) => trim($skill))
                    ->filter();
            })
            ->map(fn ($skill) => mb_strtolower($skill))
            ->filter();

        return $skills
            ->countBy()
            ->sortDesc()
            ->take($limit)
            ->map(function ($count, $skill) {
                return [
                    'name' => mb_convert_case($skill, MB_CASE_TITLE, 'UTF-8'),
                    'count' => $count,
                ];
            })
            ->values()
            ->toArray();
    }
}
