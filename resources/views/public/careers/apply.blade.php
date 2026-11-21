@extends('layouts.public')

@section('title', 'Apply — ' . $job->title)

@section('content')
<a href="{{ route('careers.show', $job) }}" class="back-link">&larr; {{ $job->title }}</a>

<div class="apply-layout">
    <div class="form-card">
        <h1>Apply for this role</h1>
        <p class="form-sub">{{ $job->title }}@if ($job->department) · {{ $job->department }}@endif · {{ $job->location ?? 'Remote' }}</p>

        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('careers.submit', $job) }}" enctype="multipart/form-data" class="application-form">
            @csrf
            <div class="field-row">
                <div class="field">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name">
                </div>
                <div class="field">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">
                </div>
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>
                <div class="field">
                    <label for="phone">Phone <span class="optional">optional</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                </div>
            </div>
            <div class="field">
                <label for="resume">Resume <span class="optional">PDF, DOC or DOCX — up to 5MB</span></label>
                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required class="file-input">
            </div>
            <div class="field">
                <label for="cover_letter">Cover letter <span class="optional">optional</span></label>
                <textarea id="cover_letter" name="cover_letter" rows="4" placeholder="A few lines on why you're a fit…">{{ old('cover_letter') }}</textarea>
            </div>
            <button class="btn btn-primary btn-block">Submit application</button>
            <p class="form-note">You'll get a confirmation email with your application reference.</p>
        </form>
    </div>
</div>
@endsection
