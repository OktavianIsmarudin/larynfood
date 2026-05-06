@if ($paginator->hasPages())
    <nav class="d-flex justify-content-center" aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0" style="gap: 2px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
