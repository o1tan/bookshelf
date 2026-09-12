@extends('layouts.app')

@section('title', '書籍ランキング | BookShelf')

@section('content')
    <!-- 評価ランキング TOP10 -->
    <div class="page-heading">
        <div>
            <h1>評価ランキング TOP10</h1>
            <p class="form-help">
                レビューの平均評価が高い書籍を表示しています。
            </p>
        </div>
    </div>

    @if ($ratedBooks->isNotEmpty())
        <div class="ranking-list">
            @foreach ($ratedBooks as $book)
                <article class="panel ranking-card">
                    <div class="ranking-position rank-{{ $loop->iteration }}">
                        {{ $loop->iteration }}
                    </div>

                    <a
                        class="ranking-cover"
                        href="{{ route('books.show', $book) }}"
                    >
                        @if ($book->image_url)
                            <img
                                src="{{ $book->image_url }}"
                                alt=""
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='grid';
                                "
                            >
                            <span style="display: none;">
                                {{ mb_substr($book->title, 0, 1) }}
                            </span>
                        @else
                            <span>
                                {{ mb_substr($book->title, 0, 1) }}
                            </span>
                        @endif
                    </a>

                    <div class="ranking-content">
                        <h2>
                            <a href="{{ route('books.show', $book) }}">
                                {{ $book->title }}
                            </a>
                        </h2>

                        <p class="book-meta">
                            {{ $book->author }}
                        </p>

                        <div class="tag-list">
                            @forelse ($book->genres as $genre)
                                <span class="tag">
                                    {{ $genre->name }}
                                </span>
                            @empty
                                <span class="book-meta">
                                    ジャンル未設定
                                </span>
                            @endforelse
                        </div>

                        <p class="book-meta">
                            レビュー：{{ $book->reviews_count }}件
                        </p>
                    </div>

                    <div class="ranking-rating">
                        <strong>
                            {{ number_format(
                                $book->reviews_avg_rating,
                                1
                            ) }}
                        </strong>
                        <span class="rating">★ / 5</span>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <section class="panel empty-state">
            評価ランキング対象の書籍はありません。
        </section>
    @endif

    <!-- お気に入りランキング TOP10 -->
    <div class="page-heading" style="margin-top: 48px;">
        <div>
            <h1>お気に入りランキング TOP10</h1>
            <p class="form-help">
                お気に入り登録数が多い書籍を表示しています。
            </p>
        </div>
    </div>

    @if ($favoriteBooks->isNotEmpty())
        <div class="ranking-list">
            @foreach ($favoriteBooks as $book)
                <article class="panel ranking-card">
                    <div class="ranking-position rank-{{ $loop->iteration }}">
                        {{ $loop->iteration }}
                    </div>

                    <a
                        class="ranking-cover"
                        href="{{ route('books.show', $book) }}"
                    >
                        @if ($book->image_url)
                            <img
                                src="{{ $book->image_url }}"
                                alt=""
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='grid';
                                "
                            >
                            <span style="display: none;">
                                {{ mb_substr($book->title, 0, 1) }}
                            </span>
                        @else
                            <span>
                                {{ mb_substr($book->title, 0, 1) }}
                            </span>
                        @endif
                    </a>

                    <div class="ranking-content">
                        <h2>
                            <a href="{{ route('books.show', $book) }}">
                                {{ $book->title }}
                            </a>
                        </h2>

                        <p class="book-meta">
                            {{ $book->author }}
                        </p>

                        <div class="tag-list">
                            @forelse ($book->genres as $genre)
                                <span class="tag">
                                    {{ $genre->name }}
                                </span>
                            @empty
                                <span class="book-meta">
                                    ジャンル未設定
                                </span>
                            @endforelse
                        </div>

                        <p class="book-meta">
                            お気に入り：{{ $book->favorites_count }}件
                        </p>
                    </div>

                    <div class="ranking-rating">
                        <strong>
                            {{ $book->favorites_count }}
                        </strong>
                        <span class="rating">❤ / -</span>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <section class="panel empty-state">
            お気に入りランキング対象の書籍はありません。
        </section>
    @endif
@endsection
