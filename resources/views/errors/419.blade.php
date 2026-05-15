@extends('layouts.app')
@section('title', 'Session Expired')
@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center max-w-sm">
        <div class="w-20 h-20 bg-amber-50 rounded-2xl flex items-center
                    justify-center mx-auto mb-5">
            <svg class="w-9 h-9 text-amber-400" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="font-display text-2xl text-ink-900 mb-2">Session Expired</h1>
        <p class="text-ink-500 text-sm mb-6">
            Your session has timed out for security reasons. Please refresh and try again.
        </p>
        <a href="{{ url()->previous() }}" class="btn-primary">Refresh Page</a>
    </div>
</div>
@endsection