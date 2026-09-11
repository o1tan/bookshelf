@extends('layouts.app')

@section('title', 'マイ読書レポート | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>マイ読書レポート</h1>
        </div>
    </div>

    <h2 class="section-title">基本情報</h2>

    <div class="report-stats">
        <article class="panel report-stat">
            <strong class="report-number report-number-blue">
                {{ $readingPlans->count() }}
            </strong>
            <span>読書計画</span>
        </article>

        <article class="panel report-stat">
            <strong class="report-number report-number-green">
                {{ $statusCounts['completed'] }}
            </strong>
            <span>読了冊数</span>
        </article>

        <article class="panel report-stat">
            <strong class="report-number report-number-orange">
                {{ $averageRating !== null
                    ? number_format($averageRating, 1)
                    : '-' }}
            </strong>
            <span>平均評価</span>
        </article>
    </div>

    <div class="report-grid">
        <section class="panel">
            <h2 class="panel-title">好きなジャンル</h2>

            @php
                $maxGenreCount = $favoriteGenres->max('count') ?: 1;
            @endphp

            <div class="genre-ranking">
                @forelse ($favoriteGenres as $index => $favoriteGenre)
                    <div class="genre-rank-row">
                        <span class="genre-rank-number">
                            {{ $index + 1 }}
                        </span>

                        <div class="genre-rank-content">
                            <div class="genre-rank-label">
                                <span>
                                    {{ $favoriteGenre['genre']->name }}
                                </span>

                                <span>
                                    {{ $favoriteGenre['count'] }}冊
                                </span>
                            </div>

                            <div class="genre-bar">
                                <span
                                    style="width: {{
                                        ($favoriteGenre['count']
                                        / $maxGenreCount) * 100
                                    }}%;"
                                ></span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-text">
                        集計できるジャンルはありません。
                    </p>
                @endforelse
            </div>
        </section>

        <section class="panel">
            <h2 class="panel-title">高評価書籍 TOP5</h2>

            <div class="top-book-list">
                @forelse ($highRatedReviews as $index => $review)
                    <a
                        class="top-book-row"
                        href="{{ route('books.show', $review->book) }}"
                    >
                        <span class="rank-circle">
                            {{ $index + 1 }}
                        </span>

                        <span class="top-book-info">
                            <strong>{{ $review->book->title }}</strong>
                            <small>{{ $review->book->author }}</small>
                        </span>

                        <span class="rating">
                            ★ {{ number_format($review->rating, 1) }}
                        </span>
                    </a>
                @empty
                    <p class="empty-text">
                        評価した書籍はありません。
                    </p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="panel">
        <div class="panel-heading">
            <h2 class="panel-title">レビュー評価書籍 TOP5</h2>

            <span class="book-meta">
                投稿レビュー：{{ $reviewCount }}件
            </span>
        </div>

        @if ($highRatedReviews->isNotEmpty())
            <div class="report-book-grid">
                @foreach ($highRatedReviews as $review)
                    <article class="report-book-card">
                        <div class="report-book-rank">
                            {{ $loop->iteration }}
                        </div>

                        <div>
                            <h3>
                                <a href="{{ route(
                                    'books.show',
                                    $review->book
                                ) }}">
                                    {{ $review->book->title }}
                                </a>
                            </h3>

                            <p class="book-meta">
                                {{ $review->book->author }}
                            </p>
                        </div>

                        <strong class="rating">
                            ★ {{ number_format($review->rating, 1) }}
                        </strong>
                    </article>
                @endforeach
            </div>
        @else
            <p class="empty-text">
                評価した書籍はありません。
            </p>
        @endif
    </section>
@endsection