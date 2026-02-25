@if ($paginator->hasPages())
    <nav>
        <ul class="pagination" style="justify-content: center; gap: 0.5rem; list-style: none; padding: 0; margin: 1rem 0;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span style="padding: 0.4rem 0.8rem; border: 1px solid #ddd; border-radius: 4px; color: #999; cursor: not-allowed; display: inline-block;">Prev</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="padding: 0.4rem 0.8rem; border: 1px solid #667eea; border-radius: 4px; color: #667eea; background: white; text-decoration: none; display: inline-block;">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li aria-disabled="true"><span style="padding: 0.4rem 0.8rem; border: 1px solid #ddd; border-radius: 4px; color: #999; display: inline-block;">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page"><span style="padding: 0.4rem 0.8rem; border: 1px solid #667eea; border-radius: 4px; background: #667eea; color: white; font-weight: bold; display: inline-block;">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" style="padding: 0.4rem 0.8rem; border: 1px solid #ddd; border-radius: 4px; color: #667eea; background: white; text-decoration: none; display: inline-block;">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="padding: 0.4rem 0.8rem; border: 1px solid #667eea; border-radius: 4px; color: #667eea; background: white; text-decoration: none; display: inline-block;">Next</a>
                </li>
            @else
                <li aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span style="padding: 0.4rem 0.8rem; border: 1px solid #ddd; border-radius: 4px; color: #999; cursor: not-allowed; display: inline-block;">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
