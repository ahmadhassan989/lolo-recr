@extends('layouts.public')

@section('title', $job->title)

@section('content')
    <a href="{{ route('careers.index') }}" class="back-link">&larr; Back to Careers</a>

    <h2>{{ $job->title }}</h2>
    <p><strong>Department:</strong> {{ $job->department ?? '—' }}</p>
    <p><strong>Location:</strong> {{ $job->location ?? 'Remote' }}</p>

    <div class="desc">
        <h4>Description</h4>
        <p>{!! $job->description ? nl2br(e($job->description)) : 'Not provided.' !!}</p>

        <h4>Requirements</h4>
        <p>{!! $job->requirements ? nl2br(e($job->requirements)) : 'Not provided.' !!}</p>

        <h4>Skills</h4>
        <p>{!! $job->skills ? nl2br(e($job->skills)) : 'Not provided.' !!}</p>
    </div>

    <a href="{{ route('careers.apply', $job) }}" class="btn-primary">Apply Now</a>
@endsection
