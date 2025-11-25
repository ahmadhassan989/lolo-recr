<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display high-level system statistics.
     */
    public function index(Request $request): View
    {
        $this->authorize('projects.view');

        $user = $request->user();
        $projectIds = $user->accessibleProjectIds();
        $dateFrom = $request->filled('from') ? $request->date('from') : null;
        $dateTo = $request->filled('to') ? $request->date('to') : null;

        $stats = [
            'candidates' => $this->candidateCount($projectIds, $dateFrom, $dateTo),
            'open_projects' => $this->openProjectCount($projectIds),
            'applications' => $this->applicationCount($projectIds, $dateFrom, $dateTo),
            'hired' => $this->hiredCount($projectIds, $dateFrom, $dateTo),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'filters' => [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ]);
    }

    protected function candidateCount(?Collection $projectIds, $dateFrom = null, $dateTo = null): int
    {
        if (! $projectIds) {
            return Candidate::query()
                ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
                ->count();
        }

        if ($projectIds->isEmpty()) {
            return 0;
        }

        return Candidate::whereHas('applications', function ($query) use ($projectIds) {
            $query->whereIn('project_id', $projectIds->all());
        })
        ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
        ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
        ->count();
    }

    protected function openProjectCount(?Collection $projectIds): int
    {
        if (! $projectIds) {
            return Project::where('status', 'open')->count();
        }

        if ($projectIds->isEmpty()) {
            return 0;
        }

        return Project::where('status', 'open')
            ->whereIn('id', $projectIds->all())
            ->count();
    }

    protected function applicationCount(?Collection $projectIds, $dateFrom = null, $dateTo = null): int
    {
        if (! $projectIds) {
            return Application::query()
                ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
                ->count();
        }

        if ($projectIds->isEmpty()) {
            return 0;
        }

        return Application::whereIn('project_id', $projectIds->all())
            ->when($dateFrom, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->count();
    }

    protected function hiredCount(?Collection $projectIds, $dateFrom = null, $dateTo = null): int
    {
        if (! $projectIds) {
            return Application::query()
                ->where('status', 'hired')
                ->when($dateFrom, fn ($query) => $query->whereDate('updated_at', '>=', $dateFrom))
                ->when($dateTo, fn ($query) => $query->whereDate('updated_at', '<=', $dateTo))
                ->count();
        }

        if ($projectIds->isEmpty()) {
            return 0;
        }

        return Application::where('status', 'hired')
            ->whereIn('project_id', $projectIds->all())
            ->when($dateFrom, fn ($query) => $query->whereDate('updated_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('updated_at', '<=', $dateTo))
            ->count();
    }
}
