@if ($paginator->hasPages())
    <nav>
        <ul class="pagination pagination-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link">{{ __('pagination.previous') }}</span></li>
            @else
                <li class="page-item"><a class="page-link ajax-pagination" href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ __('pagination.previous') }}</a></li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link ajax-pagination" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link ajax-pagination" href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('pagination.next') }}</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">{{ __('pagination.next') }}</span></li>
            @endif
        </ul>
    </nav>
@endif
