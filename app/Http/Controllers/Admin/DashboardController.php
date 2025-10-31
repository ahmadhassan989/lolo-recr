<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display high-level system statistics.
     */
    public function index(): View
    {
        $this->authorize('projects.view');

        $stats = [
            'candidates' => Candidate::count(),
            'open_projects' => Project::where('status', 'open')->count(),
            'applications' => Application::count(),
            'hired' => Application::where('status', 'hired')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
