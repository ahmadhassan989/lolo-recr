<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Job Offer</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; margin: 40px; }
        h1 { color: #0f172a; margin-bottom: 20px; }
        p { margin: 6px 0; }
        .muted { color: #6b7280; font-size: 12px; }
        hr { margin: 24px 0; border: none; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <h1>Job Offer</h1>
    <p><strong>Candidate:</strong> {{ $offer->candidate->first_name }} {{ $offer->candidate->last_name }}</p>
    <p><strong>Project:</strong> {{ $offer->project->title ?? '-' }}</p>
    <p><strong>Position:</strong> {{ $offer->position_title }}</p>
    <p><strong>Salary:</strong> {{ $offer->salary ? number_format($offer->salary, 2) : '—' }} {{ $offer->currency }}</p>
    <p><strong>Start Date:</strong> {{ optional($offer->start_date)->format('M d, Y') ?? '—' }}</p>
    <p><strong>Contract Duration:</strong> {{ $offer->contract_duration ?? '—' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($offer->status) }}</p>
    <p><strong>Notes:</strong> {{ $offer->notes ?? '—' }}</p>

    <hr>

    <p class="muted">Created by {{ $offer->creator->name ?? '—' }} on {{ $offer->created_at->format('M d, Y') }}</p>
</body>
</html>
