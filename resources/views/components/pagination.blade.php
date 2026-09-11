@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
    @endphp

    <nav
        class="simple-pagination"
        aria-label="ページ切り替え"
    >
        @if ($paginator->onFirstPage())
            <span class="button button-secondary is-disabled">
                前へ
            </span>
        @else
            <a
                class="button button-secondary"
                href="{{ $paginator->previousPageUrl() }}"
            >
                前へ
            </a>
        @endif

        <div class="pagination-numbers">
            @if ($startPage > 1)
                <a
                    class="pagination-page"
                    href="{{ $paginator->url(1) }}"
                >
                    1
                </a>

                @if ($startPage > 2)
                    <span class="pagination-ellipsis">…</span>
                @endif
            @endif

            @foreach (
                $paginator->getUrlRange($startPage, $endPage)
                as $page => $url
            )
                @if ($page === $currentPage)
                    <span
                        class="pagination-page is-current"
                        aria-current="page"
                    >
                        {{ $page }}
                    </span>
                @else
                    <a
                        class="pagination-page"
                        href="{{ $url }}"
                    >
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <span class="pagination-ellipsis">…</span>
                @endif

                <a
                    class="pagination-page"
                    href="{{ $paginator->url($lastPage) }}"
                >
                    {{ $lastPage }}
                </a>
            @endif
        </div>

        @if ($paginator->hasMorePages())
            <a
                class="button button-secondary"
                href="{{ $paginator->nextPageUrl() }}"
            >
                次へ
            </a>
        @else
            <span class="button button-secondary is-disabled">
                次へ
            </span>
        @endif
    </nav>
@endif