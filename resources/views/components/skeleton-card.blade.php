{{-- Product card skeleton for loading states --}}
<div class="card overflow-hidden animate-pulse">
    <div class="aspect-square bg-ink-100"></div>
    <div class="p-3.5 space-y-2.5">
        <div class="h-2.5 bg-ink-100 rounded-full w-1/3"></div>
        <div class="h-3.5 bg-ink-100 rounded-full w-full"></div>
        <div class="h-3.5 bg-ink-100 rounded-full w-3/4"></div>
        <div class="flex gap-1 pt-1">
            @for($i = 0; $i < 5; $i++)
                <div class="w-3 h-3 bg-ink-100 rounded-full"></div>
            @endfor
        </div>
        <div class="flex items-center justify-between pt-1">
            <div class="h-5 bg-ink-100 rounded-full w-16"></div>
            <div class="w-8 h-8 bg-ink-100 rounded-lg"></div>
        </div>
    </div>
</div>