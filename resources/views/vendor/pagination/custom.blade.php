@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; gap: 0.5rem; align-items: center;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 0.5rem 1rem; background: #112240; color: var(--text-muted); border-radius: 8px; border: 1px solid var(--border-color); cursor: not-allowed;">
                <i class="fa-solid fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding: 0.5rem 1rem; background: var(--surface-color); color: var(--primary-color); border-radius: 8px; border: 1px solid var(--primary-color); text-decoration: none; transition: all 0.2s;">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span style="color: var(--text-muted); padding: 0.5rem;">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding: 0.5rem 1rem; background: var(--primary-color); color: var(--bg-color); border-radius: 8px; font-weight: bold; border: 1px solid var(--primary-color);">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="padding: 0.5rem 1rem; background: var(--surface-color); color: var(--text-main); border-radius: 8px; border: 1px solid var(--border-color); text-decoration: none; transition: all 0.2s;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding: 0.5rem 1rem; background: var(--surface-color); color: var(--primary-color); border-radius: 8px; border: 1px solid var(--primary-color); text-decoration: none; transition: all 0.2s;">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <span style="padding: 0.5rem 1rem; background: #112240; color: var(--text-muted); border-radius: 8px; border: 1px solid var(--border-color); cursor: not-allowed;">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif
