@extends('layouts.app')
@section('title', 'Server Error')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md mx-auto">
        <div class="w-40 h-40 bg-red-50 rounded-full flex items-center
                    justify-center mx-auto mb-8">
            <span class="text-6xl select-none">⚡</span>
        </div>
        <h1 class="font-display text-3xl text-ink-900 mb-3">
            Something Went Wrong
        </h1>
        <p class="text-ink-500 text-sm leading-relaxed mb-8">
            We're experiencing a temporary issue. Our team has been notified
            and is working to fix it. Please try again in a moment.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}" class="btn-primary">
                Go Back
            </a>
            <a href="{{ url('/') }}" class="btn-secondary">
                Home
            </a>
        </div>
    </div>
</div>
@endsection