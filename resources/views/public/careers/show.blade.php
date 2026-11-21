@extends('layouts.public')

@section('title', $job->title)

@section('content')
<a href="{{ route('careers.index') }}" class="back-link">&larr; All positions</a>

<div class="job-layout">
    <div class="job-main">
        <div class="job-card-top">
            @if ($job->department)
                <span class="badge">{{ $job->department }}</span>
            @endif
            @if ($job->employment_type)
                <span class="badge badge-muted">{{ str_replace('_', ' ', Str::title($job->employment_type)) }}</span>
            @endif
        </div>
        <h1 class="job-heading">{{ $job->title }}</h1>

        <section class="job-section">
            <h4>About the role</h4>
            <div class="rich-text">{!! $job->description ? nl2br(e($job->description)) : 'Details will be shared during the interview.' !!}</div>
        </section>

        @if ($job->requirements)
            <section class="job-section">
                <h4>What you'll bring</h4>
                <div class="rich-text">{!! nl2br(e($job->requirements)) !!}</div>
            </section>
        @endif

        @if ($job->skills)
            <section class="job-section">
                <h4>Skills</h4>
                <div class="skill-chips">
                    @foreach (array_filter(array_map('trim', preg_split('/[,\n]+/', $job->skills))) as $skill)
                        <span class="chip">{{ $skill }}</span>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <aside class="job-aside">
        <div class="apply-card">
            <ul class="apply-facts">
                <li><span>Location</span><strong>{{ $job->location ?? 'Remote' }}</strong></li>
                @if ($job->employment_type)
                    <li><span>Type</span><strong>{{ str_replace('_', ' ', Str::title($job->employment_type)) }}</strong></li>
                @endif
                @if ($job->salary_range)
                    <li><span>Salary</span><strong>{{ $job->salary_range }}</strong></li>
                @endif
                @if ($job->deadline)
                    <li><span>Apply by</span><strong class="{{ $job->deadline->diffInDays(now()) <= 7 ? 'meta-urgent' : '' }}">{{ $job->deadline->format('M j, Y') }}</strong></li>
                @endif
            </ul>
            <a href="{{ route('careers.apply', $job) }}" class="btn btn-primary btn-block">Apply for this role</a>
            <p class="apply-note">Takes about 2 minutes — resume + a few details.</p>
        </div>
    </aside>
</div>
@endsection
