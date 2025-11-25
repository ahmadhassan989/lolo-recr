<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectTeamController;
use App\Http\Controllers\Admin\ReportsExportController;
use App\Http\Controllers\Admin\RecruiterPerformanceController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateRatingController;
use App\Http\Controllers\CandidateTagController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Public\CareersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CareersController::class, 'index'])->name('home');
Route::get('/careers', [CareersController::class, 'index'])->name('careers.index');
Route::get('/careers/{job}', [CareersController::class, 'show'])->name('careers.show');
Route::get('/careers/{job}/apply', [CareersController::class, 'applyForm'])->name('careers.apply');
Route::post('/careers/{job}/apply', [CareersController::class, 'submitApplication'])->name('careers.submit');
Route::get('/careers/thanks', [CareersController::class, 'thankYou'])->name('careers.thanks');

Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::resource('candidates', CandidateController::class);

    Route::post('candidates/{candidate}/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::put('interviews/{interview}', [InterviewController::class, 'update'])->name('interviews.update');
    Route::delete('interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews.destroy');

    Route::post('candidates/{candidate}/rating', CandidateRatingController::class)->name('candidates.rate');
    Route::post('candidates/{candidate}/tags', CandidateTagController::class)->name('candidates.tags.update');

    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus'])
        ->name('applications.updateStatus');

    Route::post('candidates/{candidate}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('admin/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');
    Route::get('admin/analytics', AnalyticsController::class)
        ->middleware('can:analytics.view')
        ->name('admin.analytics');

    Route::get('admin/reports/export/candidates', [ReportsExportController::class, 'exportCandidatesByMonth'])
        ->middleware('can:analytics.view')
        ->name('admin.reports.candidates');
    Route::get('admin/reports/export/skills', [ReportsExportController::class, 'exportTopSkills'])
        ->middleware('can:analytics.view')
        ->name('admin.reports.skills');
    Route::get('admin/reports/export/interviews', [ReportsExportController::class, 'exportInterviewResults'])
        ->middleware('can:analytics.view')
        ->name('admin.reports.interviews');

    Route::get('admin/reports/recruiters', [RecruiterPerformanceController::class, 'index'])
        ->middleware('can:analytics.view')
        ->name('admin.reports.recruiters');
    Route::get('admin/reports/recruiters/export', [RecruiterPerformanceController::class, 'exportCsv'])
        ->middleware('can:analytics.view')
        ->name('admin.reports.recruiters.export');

    Route::middleware('role:super_admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', UserManagementController::class)
                ->only(['index', 'create', 'store', 'edit', 'update']);

            Route::resource('roles', RolePermissionController::class)
                ->only(['index', 'create', 'store', 'edit', 'update']);

            Route::resource('project-teams', ProjectTeamController::class)
                ->parameters(['project-teams' => 'project'])
                ->only(['index', 'edit', 'update']);
        });

    Route::get('job-offers', [JobOfferController::class, 'index'])
        ->middleware('can:offers.view')
        ->name('job-offers.index');
    Route::get('job-offers/create', [JobOfferController::class, 'create'])
        ->middleware('can:offers.manage')
        ->name('job-offers.create');
    Route::post('job-offers', [JobOfferController::class, 'store'])
        ->middleware('can:offers.manage')
        ->name('job-offers.store');
    Route::get('job-offers/{jobOffer}', [JobOfferController::class, 'show'])
        ->middleware('can:offers.view')
        ->name('job-offers.show');
    Route::get('job-offers/{jobOffer}/export', [JobOfferController::class, 'export'])
        ->middleware('can:offers.view')
        ->name('job-offers.export');
    Route::put('job-offers/{jobOffer}/status', [JobOfferController::class, 'updateStatus'])
        ->middleware('can:offers.manage')
        ->name('job-offers.status');

    Route::get('jobs', [JobController::class, 'index'])
        ->middleware('can:jobs.view')
        ->name('jobs.index');
    Route::get('jobs/create', [JobController::class, 'create'])
        ->middleware('can:jobs.manage')
        ->name('jobs.create');
    Route::post('jobs', [JobController::class, 'store'])
        ->middleware('can:jobs.manage')
        ->name('jobs.store');
    Route::get('jobs/{job}', [JobController::class, 'show'])
        ->middleware('can:jobs.view')
        ->name('jobs.show');
    Route::get('jobs/{job}/edit', [JobController::class, 'edit'])
        ->middleware('can:jobs.manage')
        ->name('jobs.edit');
    Route::put('jobs/{job}', [JobController::class, 'update'])
        ->middleware('can:jobs.manage')
        ->name('jobs.update');
    Route::delete('jobs/{job}', [JobController::class, 'destroy'])
        ->middleware('can:jobs.manage')
        ->name('jobs.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
