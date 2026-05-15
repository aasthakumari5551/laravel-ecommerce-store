@props(['count' => 1])

@for($i = 0; $i < $count; $i++)
    <div class="card overflow-hidden animate-pulse" aria-hidden="true" role="presentation">
        <div class="aspect-square bg-gradient-to-br from-ink-100 to-ink-200"></div>
        <div class="p-3.5 space-y-2.5">
            <div class="h-2 bg-ink-100 rounded-full w-1/4"></div>
            <div class="h-3.5 bg-ink-100 rounded-full w-full"></div>
            <div class="h-3.5 bg-ink-100 rounded-full w-3/4"></div>
            <div class="flex gap-0.5 mt-1">
                @for($s = 0; $s < 5; $s++)
                    <div class="w-3 h-3 bg-ink-100 rounded-full"></div>
                @endfor
            </div>
            <div class="flex items-center justify-between pt-1">
                <div class="h-5 bg-ink-100 rounded-full w-16"></div>
                <div class="w-8 h-8 bg-ink-100 rounded-lg"></div>
            </div>
        </div>
    </div>
@endfor