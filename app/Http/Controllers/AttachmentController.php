<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Candidate;
use App\Models\CandidateLog;
use App\Services\CvParserService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * Store a new attachment for the candidate.
     */
    public function store(Request $request, Candidate $candidate): RedirectResponse
    {
        $this->authorize('candidates.manage');

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:15360'], // 15 MB max
            'type' => ['required', Rule::in(['cv', 'certificate', 'id', 'other'])],
            'file_name' => ['nullable', 'string', 'max:255'],
        ]);

        $storedPath = $request->file('file')->store('attachments', 'public');

        $attachment = $candidate->attachments()->create([
            'file_path' => $storedPath,
            'file_name' => $validated['file_name'] ?? $request->file('file')->getClientOriginalName(),
            'type' => $validated['type'],
            'uploaded_by' => $request->user()->id,
        ]);

        CandidateLog::create([
            'candidate_id' => $candidate->id,
            'action' => 'Uploaded attachment',
            'performed_by' => $request->user()->id,
            'notes' => sprintf('Type: %s, File: %s', strtoupper($attachment->type), $attachment->file_name),
        ]);

        if ($attachment->type === 'cv') {
            $parser = app(CvParserService::class);
            $parsed = $parser->parse(Storage::disk('public')->path($storedPath));

            $updates = [];
            $updatedFields = [];

            if (!empty($parsed['skills'])) {
                $existingSkills = collect(explode(',', (string) $candidate->skills))->map(fn ($skill) => trim($skill))->filter();
                $parsedSkills = collect(explode(',', (string) $parsed['skills']))->map(fn ($skill) => trim($skill))->filter();

                $mergedSkills = $existingSkills->merge($parsedSkills)->filter()->map(fn ($skill) => ucfirst(strtolower($skill)))->unique()->implode(', ');

                if ($mergedSkills && $mergedSkills !== $candidate->skills) {
                    $updates['skills'] = $mergedSkills;
                    $updatedFields[] = 'skills';
                }
            }

            if (!empty($parsed['experience_years']) && (int) $parsed['experience_years'] > (int) $candidate->experience_years) {
                $updates['experience_years'] = (int) $parsed['experience_years'];
                $updatedFields[] = 'experience_years';
            }

            if (!empty($parsed['education_level']) && empty($candidate->education_level)) {
                $updates['education_level'] = $parsed['education_level'];
                $updatedFields[] = 'education_level';
            }

            if (!empty($updates)) {
                $candidate->fill($updates)->save();
            }

            CandidateLog::create([
                'candidate_id' => $candidate->id,
                'action' => 'Parsed CV',
                'performed_by' => $request->user()->id,
                'notes' => empty($updatedFields)
                    ? 'CV parsed; no profile updates applied.'
                    : 'Updated fields: ' . implode(', ', $updatedFields),
            ]);
        }

        return back()->with('status', 'Attachment uploaded successfully.');
    }

    /**
     * Download an attachment.
     */
    public function download(Attachment $attachment): StreamedResponse
    {
        $this->authorize('candidates.view');

        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Remove an attachment.
     */
    public function destroy(Attachment $attachment): RedirectResponse
    {
        $this->authorize('candidates.manage');

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('status', 'Attachment deleted successfully.');
    }
}
