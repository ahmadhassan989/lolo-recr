@extends('layouts.public')

@section('title', 'Open Positions')

@section('content')
    <h2 class="page-title">Join Our Team</h2>

    <div class="jobs-list">
        @forelse ($jobs as $job)
            <div class="job-card">
                <h3>{{ $job->title }}</h3>
                <p><strong>Department:</strong> {{ $job->department ?? '—' }}</p>
                <p><strong>Location:</strong> {{ $job->location ?? 'Remote' }}</p>
                <a href="{{ route('careers.show', $job) }}" class="btn">View Details</a>
            </div>
        @empty
            <p>No open positions at the moment. Please check back soon.</p>
        @endforelse
    </div>

    {{ $jobs->links() }}
@endsection
