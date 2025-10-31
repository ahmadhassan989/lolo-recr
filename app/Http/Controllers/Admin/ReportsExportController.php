<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Interview;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsExportController extends Controller
{
    public function exportCandidatesByMonth(): StreamedResponse
    {
        Gate::authorize('analytics.view');

        $rows = Candidate::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $filename = 'candidates_by_month_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Month', 'Total']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->month, $row->total]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportTopSkills(): StreamedResponse
    {
        Gate::authorize('analytics.view');

        $skills = Candidate::query()
            ->whereNotNull('skills')
            ->pluck('skills')
            ->flatMap(function (string $skillLine) {
                return collect(explode(',', $skillLine))
                    ->map(fn ($skill) => trim($skill))
                    ->filter();
            })
            ->map(fn ($skill) => mb_strtolower($skill))
            ->filter()
            ->countBy()
            ->sortDesc();

        $filename = 'top_skills_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($skills) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Skill', 'Count']);
            foreach ($skills as $skill => $count) {
                fputcsv($out, [mb_convert_case($skill, MB_CASE_TITLE, 'UTF-8'), $count]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportInterviewResults(): StreamedResponse
    {
        Gate::authorize('analytics.view');

        $rows = Interview::query()
            ->selectRaw('result, COUNT(*) as total')
            ->groupBy('result')
            ->orderBy('result')
            ->get();

        $filename = 'interview_results_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Result', 'Total']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->result, $row->total]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
