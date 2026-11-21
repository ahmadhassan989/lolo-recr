@extends('layouts.public')

@section('title', 'Application Received')

@section('content')
<div class="thanks-card">
    <div class="thanks-icon">&#10003;</div>
    <h1>Application received</h1>
    <p>Thanks for applying — our team will review your application and reach out by email if there's a fit. A confirmation is on its way to your inbox.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Browse more positions</a>
</div>
@endsection
