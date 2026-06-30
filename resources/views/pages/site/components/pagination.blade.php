@if ($paginator->hasPages())
    <nav
        aria-label="Pagination"
        class="flex flex-col items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 sm:flex-row sm:justify-between"
    >
        <span class="text-xs text-slate-500">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </span>

        <div class="flex flex-wrap items-center justify-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-300">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">Prev</a>
            @endif

            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="rounded-md px-3 py-1.5 text-xs font-medium text-white shadow-sm" style="background-color: var(--color-accent);">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">Next</a>
            @else
                <span class="rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-300">Next</span>
            @endif
        </div>
    </nav>
@endif
