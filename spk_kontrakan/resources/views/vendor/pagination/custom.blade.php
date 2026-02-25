@if ($paginator->hasPages())
<nav aria-label="Page navigation" class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3">
    {{-- Info Pagination --}}
    <div class="pagination-info d-flex align-items-center gap-2">
        <span class="text-muted small">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </span>
    </div>

    {{-- Navigation - Page Numbers Only --}}
    <div class="d-flex align-items-center gap-1" style="list-style: none; padding: 0; margin: 0;">
        {{-- Previous Button --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-sm btn-outline-secondary disabled" aria-disabled="true" style="border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.85rem; cursor: not-allowed; opacity: 0.5;">
                Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm" style="background: #667eea; border: 1px solid #667eea; border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.85rem; font-weight: 600; color: white; text-decoration: none;" rel="prev">
                Prev
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="btn btn-sm" style="border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.85rem; color: #999; cursor: default; border: 1px solid #ddd;">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn btn-sm" style="background: #667eea; color: white; border: 1px solid #667eea; border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.85rem; font-weight: 700;" aria-current="page">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="btn btn-sm" style="border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.85rem; font-weight: 600; color: #667eea; border: 1px solid #ddd; text-decoration: none; background: white;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm" style="background: #667eea; border: 1px solid #667eea; border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.85rem; font-weight: 600; color: white; text-decoration: none;" rel="next">
                Next
            </a>
        @else
            <span class="btn btn-sm btn-outline-secondary disabled" aria-disabled="true" style="border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.85rem; cursor: not-allowed; opacity: 0.5;">
                Next
            </span>
        @endif
    </div>
</nav>
@endif
