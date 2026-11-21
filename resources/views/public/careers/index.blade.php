@extends('layouts.public')

@section('title', 'Open Positions')

@section('content')
<section class="hero">
    <h1>Join Our Team</h1>
    <p class="hero-sub">{{ $jobs->total() }} open {{ Str::plural('position', $jobs->total()) }} — find the one that fits you.</p>
</section>

<form method="GET" action="{{ route('careers.index') }}" class="filters">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search title, skill or department…" class="filter-search">
    @if ($departments->isNotEmpty())
        <select name="department" class="filter-select">
            <option value="">All departments</option>
            @foreach ($departments as $department)
                <option value="{{ $department }}" @selected(request('department') === $department)>{{ $department }}</option>
            @endforeach
        </select>
    @endif
    @if ($types->isNotEmpty())
        <select name="type" class="filter-select">
            <option value="">Any type</option>
            @foreach ($types as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', Str::title($type)) }}</option>
            @endforeach
        </select>
    @endif
    <button type="submit" class="btn btn-primary">Search</button>
    @if (request()->hasAny(['q', 'department', 'type']))
        <a href="{{ route('careers.index') }}" class="filter-clear">Clear</a>
    @endif
</form>

<div class="jobs-grid">
    @forelse ($jobs as $job)
        <article class="job-card">
            <div class="job-card-top">
                @if ($job->department)
                    <span class="badge">{{ $job->department }}</span>
                @endif
                @if ($job->employment_type)
                    <span class="badge badge-muted">{{ str_replace('_', ' ', Str::title($job->employment_type)) }}</span>
                @endif
            </div>
            <h3 class="job-title"><a href="{{ route('careers.show', $job) }}">{{ $job->title }}</a></h3>
            <ul class="job-meta">
                <li>{{ $job->location ?? 'Remote' }}</li>
                @if ($job->salary_range)
                    <li>{{ $job->salary_range }}</li>
                @endif
                @if ($job->deadline)
                    <li class="{{ $job->deadline->diffInDays(now()) <= 7 ? 'meta-urgent' : '' }}">
                        Closes {{ $job->deadline->format('M j') }}
                    </li>
                @endif
            </ul>
            @if ($job->skills)
                <div class="skill-chips">
                    @foreach (array_slice(array_filter(array_map('trim', preg_split('/[,\n]+/', $job->skills))), 0, 4) as $skill)
                        <span class="chip">{{ $skill }}</span>
                    @endforeach
                </div>
            @endif
            <a href="{{ route('careers.show', $job) }}" class="btn btn-ghost">View position &rarr;</a>
        </article>
    @empty
        <div class="empty-state">
            <h3>No positions match your search</h3>
            <p>Try different keywords or <a href="{{ route('careers.index') }}">browse all openings</a>.</p>
        </div>
    @endforelse
</div>

{{ $jobs->links() }}
@endsection
