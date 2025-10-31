@extends('layouts.public')

@section('title', 'Apply for ' . $job->title)

@section('content')
    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Apply for {{ $job->title }}</h2>

    <form method="POST" action="{{ route('careers.submit', $job) }}" enctype="multipart/form-data" class="application-form">
        @csrf
        <label>First Name</label>
        <input type="text" name="first_name" value="{{ old('first_name') }}" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name') }}" required>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}">

        <label>Resume (PDF/DOC)</label>
        <input type="file" name="resume" accept=".pdf,.doc,.docx" required>

        <label>Cover Letter</label>
        <textarea name="cover_letter" rows="4">{{ old('cover_letter') }}</textarea>

        <button class="btn-primary">Submit Application</button>
    </form>
@endsection
