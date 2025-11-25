<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Display recruiting analytics.
     */
    public function __invoke(Request $request): View
    {
        $dateFrom = $request->filled('from') ? $request->date('from') : null;
        $dateTo = $request->filled('to') ? $request->date('to') : null;

        $totalCandidates = Candidate::query()
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->count();

        $totalProjects = Project::query()
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->count();

        $totalApplications = Application::query()
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->count();

        $totalHired = Application::query()
            ->where('status', 'hired')
            ->when($dateFrom, fn ($query) => $query->whereDate('updated_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('updated_at', '<=', $dateTo))
            ->count();

        $monthlyCandidates = Candidate::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as total')
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $monthlyChart = $this->formatMonthlyChart($monthlyCandidates);

        $topSkills = $this->topSkills($dateFrom, $dateTo);

        $interviewStats = Interview::query()
            ->selectRaw('result, COUNT(*) as total')
            ->when($dateFrom, fn ($query) => $query->whereDate('interview_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('interview_date', '<=', $dateTo))
            ->groupBy('result')
            ->pluck('total', 'result');

        $pipelineStats = Application::query()
            ->selectRaw('status, COUNT(*) as total')
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $teamPerformance = $this->teamPerformance();

        return view('admin.analytics', [
            'totalCandidates' => $totalCandidates,
            'totalProjects' => $totalProjects,
            'totalApplications' => $totalApplications,
            'totalHired' => $totalHired,
            'monthlyCandidates' => $monthlyChart,
            'topSkills' => $topSkills,
            'interviewStats' => $interviewStats->toArray(),
            'pipelineStats' => $pipelineStats,
            'teamPerformance' => $teamPerformance,
            'filters' => [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
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
    protected function topSkills(?string $from = null, ?string $to = null, int $limit = 5): array
    {
        $skills = Candidate::query()
            ->whereNotNull('skills')
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
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

    protected function teamPerformance(): array
    {
        $projectAssignments = ProjectUser::selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $candidateCounts = Candidate::selectRaw('created_by, COUNT(*) as total')
            ->groupBy('created_by')
            ->pluck('total', 'created_by');

        $hireCounts = Application::selectRaw('project_user.user_id as user_id, COUNT(*) as total')
            ->join('project_user', 'project_user.project_id', '=', 'applications.project_id')
            ->where('applications.status', 'hired')
            ->groupBy('project_user.user_id')
            ->pluck('total', 'user_id');

        $userIds = $projectAssignments->keys()
            ->merge($candidateCounts->keys())
            ->merge($hireCounts->keys())
            ->unique()
            ->toArray();

        if (empty($userIds)) {
            return [];
        }

        $users = User::with('roles')->whereIn('id', $userIds)->orderBy('name')->get();

        return $users->map(function (User $user) use ($projectAssignments, $candidateCounts, $hireCounts) {
            return [
                'name' => $user->name,
                'role' => $user->getRoleNames()->first() ?? '—',
                'projects' => $projectAssignments[$user->id] ?? 0,
                'candidates' => $candidateCounts[$user->id] ?? 0,
                'hires' => $hireCounts[$user->id] ?? 0,
            ];
        })->toArray();
    }
}
