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
                {{ $reviewCount }}
            </strong>
            <span>総レビュー数</span>
        </article>

        <article class="panel report-stat">
            <strong class="report-number report-number-green">
                {{ $completedBookCount }}
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
            <h2 class="panel-title">評価分布</h2>

            @php
                $maxRatingCount = $ratingDistribution->max() ?: 1;
            @endphp

            <div class="genre-ranking">
                @foreach ($ratingDistribution as $rating => $count)
                    <div class="genre-rank-row">
                        <span class="genre-rank-number">
                            {{ $rating }}★
                        </span>

                        <div class="genre-rank-content">
                            <div class="genre-rank-label">
                                <span>{{ $rating }}点</span>
                                <span>{{ $count }}件</span>
                            </div>

                            <div class="genre-bar">
                                <span
                                    style="width: {{
                                        ($count / $maxRatingCount) * 100
                                    }}%;"
                                ></span>
                            </div>
                        </div>
                    </div>
                @endforeach
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
                        <span class="rank-circle">{{ $index + 1 }}</span>

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
                        4点以上の評価書籍はありません。
                    </p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="panel">
        <h2 class="panel-title">ジャンル別評価傾向 TOP5</h2>

        <div class="genre-ranking">
            @forelse ($genreRatingTrends as $index => $trend)
                <a
                    class="genre-rank-row"
                    href="{{ route('genres.show', $trend['genre']) }}"
                >
                    <span class="genre-rank-number">
                        {{ $index + 1 }}
                    </span>

                    <div class="genre-rank-content">
                        <div class="genre-rank-label">
                            <span>{{ $trend['genre']->name }}</span>
                            <span>
                                平均 {{ number_format(
                                    $trend['average_rating'],
                                    1
                                ) }}点（{{ $trend['count'] }}件）
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <p class="empty-text">
                    集計できるジャンルはありません。
                </p>
            @endforelse
        </div>
    </section>
@endsection