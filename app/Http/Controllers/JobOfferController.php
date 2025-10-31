<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateLog;
use App\Models\JobOffer;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class JobOfferController extends Controller
{
    /**
     * Display a list of job offers.
     */
    public function index(): View
    {
        $this->authorize('offers.view');

        $offers = JobOffer::query()
            ->with(['candidate', 'project', 'creator'])
            ->latest()
            ->paginate(15);

        return view('job_offers.index', compact('offers'));
    }

    /**
     * Show the form for creating a new job offer.
     */
    public function create(Request $request): View
    {
        $this->authorize('offers.manage');

        $candidateId = $request->integer('candidate_id');
        abort_if(!$candidateId, 404);

        $candidate = Candidate::findOrFail($candidateId);
        $projects = Project::query()
            ->where('status', 'open')
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('job_offers.create', compact('candidate', 'projects'));
    }

    /**
     * Store a newly created job offer.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('offers.manage');

        $validated = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'position_title' => ['required', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'start_date' => ['nullable', 'date'],
            'contract_duration' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['currency'] = strtoupper($validated['currency']);

        $offer = JobOffer::create($validated + [
            'status' => 'pending',
            'created_by' => $request->user()->id,
        ]);

        CandidateLog::create([
            'candidate_id' => $offer->candidate_id,
            'action' => 'Job offer created',
            'performed_by' => $request->user()->id,
            'notes' => $offer->position_title,
        ]);

        return redirect()
            ->route('job-offers.show', $offer)
            ->with('status', 'Job offer created successfully.');
    }

    /**
     * Display a specific job offer.
     */
    public function show(JobOffer $jobOffer): View
    {
        $this->authorize('offers.view');

        $jobOffer->load(['candidate', 'project', 'creator']);

        return view('job_offers.show', ['offer' => $jobOffer]);
    }

    /**
     * Export a job offer as PDF.
     */
    public function export(JobOffer $jobOffer)
    {
        $this->authorize('offers.view');

        $jobOffer->load(['candidate', 'project', 'creator']);

        $pdf = Pdf::loadView('pdf.job_offer', ['offer' => $jobOffer]);

        return $pdf->download('JobOffer_' . $jobOffer->id . '.pdf');
    }

    /**
     * Update the status of a job offer.
     */
    public function updateStatus(Request $request, JobOffer $jobOffer): RedirectResponse
    {
        $this->authorize('offers.manage');

        $validated = $request->validate([
            'status' => ['required', 'in:pending,accepted,declined,expired'],
        ]);

        $jobOffer->update(['status' => $validated['status']]);

        CandidateLog::create([
            'candidate_id' => $jobOffer->candidate_id,
            'action' => 'Job offer ' . $validated['status'],
            'performed_by' => $request->user()->id,
            'notes' => $jobOffer->position_title,
        ]);

        return back()->with('status', 'Job offer status updated.');
    }
}
