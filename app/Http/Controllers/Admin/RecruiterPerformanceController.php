<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecruiterPerformanceController extends Controller
{
    /**
     * Display the recruiter performance dashboard.
     */
    public function index(Request $request)
    {
        Gate::authorize('analytics.view');

        [$from, $to] = $this->resolveDateRange($request);

        $performance = $this->buildPerformanceCollection($from, $to);

        return view('admin.reports.recruiters', [
            'performance' => $performance,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    /**
     * Export recruiter performance as CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        Gate::authorize('analytics.view');

        [$from, $to] = $this->resolveDateRange($request);

        $performance = $this->buildPerformanceCollection($from, $to);
        $filename = 'recruiter_performance_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($performance) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Recruiter', 'Candidates Added', 'Interviews', 'Hired', 'Conversion %']);

            foreach ($performance as $row) {
                fputcsv($out, [
                    $row['name'],
                    $row['added'],
                    $row['interviews'],
                    $row['hired'],
                    $row['conversion'],
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Build performance data for a period.
     *
     * @return Collection<int, array{id:int,name:string,added:int,interviews:int,hired:int,conversion:float}>
     */
    protected function buildPerformanceCollection(Carbon $from, Carbon $to): Collection
    {
        $recruiters = User::role('recruiter')
            ->orderBy('name')
            ->get();

        return $recruiters->map(function (User $recruiter) use ($from, $to) {
            $addedCandidates = Candidate::query()
                ->where('created_by', $recruiter->id)
                ->whereBetween('created_at', [$from, $to])
                ->pluck('id');

            $addedCount = $addedCandidates->count();

            $interviewsCount = Interview::query()
                ->where('interviewer', $recruiter->name)
                ->whereBetween('interview_date', [$from, $to])
                ->count();

            $hiredCount = $addedCount
                ? Application::query()
                    ->where('status', 'hired')
                    ->whereBetween('updated_at', [$from, $to])
                    ->whereIn('candidate_id', $addedCandidates)
                    ->count()
                : 0;

            $conversion = $addedCount > 0
                ? round(($hiredCount / $addedCount) * 100, 1)
                : 0.0;

            return [
                'id' => $recruiter->id,
                'name' => $recruiter->name,
                'added' => $addedCount,
                'interviews' => $interviewsCount,
                'hired' => $hiredCount,
                'conversion' => $conversion,
            ];
        });
    }

    /**
     * Resolve the from/to date range.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveDateRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfMonth();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }
}
