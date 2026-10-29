@props(['paginator'])
{{-- Previous / numbered pages / next, for a paginated list. --}}
@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $pages = collect(range(1, $last))->filter(fn ($p) => $p === 1 || $p === $last || abs($p - $current) <= 1)->values();
    @endphp
    <nav aria-label="Pagination" class="mt-12 flex flex-wrap items-center justify-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="chip opacity-40" aria-disabled="true">&larr; Newer</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="chip">&larr; Newer</a>
        @endif

        @foreach ($pages as $i => $page)
            @if ($i > 0 && $page - $pages[$i - 1] > 1)
                <span class="px-1 text-ink-soft" aria-hidden="true">&hellip;</span>
            @endif
            <a href="{{ $paginator->url($page) }}" class="chip {{ $page === $current ? 'is-active' : '' }}" @if ($page === $current) aria-current="page" @endif aria-label="Page {{ $page }}">{{ $page }}</a>
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="chip">Older &rarr;</a>
        @else
            <span class="chip opacity-40" aria-disabled="true">Older &rarr;</span>
        @endif
    </nav>
@endif
