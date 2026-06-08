@if ($paginator->hasPages())
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-4 py-3 border-t border-slate-100 text-xs text-slate-500">
        <span>
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </span>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded border border-slate-200 text-slate-300">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded border border-slate-200 text-slate-700 hover:bg-slate-50">Prev</a>
            @endif

            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 rounded text-white" style="background-color: var(--color-accent);">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded border border-slate-200 text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded border border-slate-200 text-slate-700 hover:bg-slate-50">Next</a>
            @else
                <span class="px-3 py-1.5 rounded border border-slate-200 text-slate-300">Next</span>
            @endif
        </div>
    </div>
@endif