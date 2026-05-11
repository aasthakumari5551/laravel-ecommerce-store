@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Review Moderation</h1>

    {{-- Status filter --}}
    <div class="flex gap-2 mb-6">
        @foreach (['', 'pending', 'approved', 'rejected'] as $s)
            <a href="{{ route('admin.reviews.index', $s ? ['status' => $s] : []) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium border transition
                      {{ request('status') === $s || (! request('status') && $s === '')
                          ? 'bg-indigo-600 text-white border-indigo-600'
                          : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300' }}">
                {{ $s ? ucfirst($s) : 'All' }}
            </a>
        @endforeach
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($reviews as $review)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        {{-- Product + user --}}
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                                {{ $review->product->name }}
                            </span>
                            @if ($review->is_verified_purchase)
                                <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                    ✓ Verified Purchase
                                </span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full
                                bg-{{ $review->status->color() }}-100 text-{{ $review->status->color() }}-700">
                                {{ $review->status->label() }}
                            </span>
                        </div>

                        {{-- Stars --}}
                        <div class="flex gap-0.5 mb-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }} text-sm">★</span>
                            @endfor
                        </div>

                        @if ($review->title)
                            <p class="font-semibold text-gray-900 text-sm">{{ $review->title }}</p>
                        @endif
                        @if ($review->body)
                            <p class="text-sm text-gray-600 mt-1">{{ $review->body }}</p>
                        @endif

                        <p class="text-xs text-gray-400 mt-2">
                            by {{ $review->user->name }} · {{ $review->created_at->diffForHumans() }}
                        </p>

                        @if ($review->rejection_reason)
                            <p class="text-xs text-red-500 mt-1">Rejection: {{ $review->rejection_reason }}</p>
                        @endif
                    </div>

                    {{-- Moderation actions --}}
                    @if ($review->status->value !== 'approved')
                        <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="approve">
                            <button type="submit"
                                    class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 transition">
                                Approve
                            </button>
                        </form>
                    @endif
                    @if ($review->status->value !== 'rejected')
                        <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}"
                              class="flex items-start gap-2">
                            @csrf @method('PATCH')
                            <input type="hidden" name="action" value="reject">
                            <input type="text" name="rejection_reason" placeholder="Reason…"
                                   class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none w-36">
                            <button type="submit"
                                    class="text-xs bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition">
                                Reject
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-12 text-center text-gray-400 text-sm">
                No reviews found.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>
</div>
@endsection