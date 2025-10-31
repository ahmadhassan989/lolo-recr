@extends('layouts.public')

@section('title', 'Thank You')

@section('content')
    <h2>Thank You!</h2>
    <p>Your application has been received successfully.</p>
    <a href="{{ route('home') }}" class="btn">Back to Careers</a>
@endsection
