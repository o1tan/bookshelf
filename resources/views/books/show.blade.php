@extends('layouts.app')

@section('title', $book->title . ' | BookShelf')

@section('content')
    <p class="back-link">
        <a href="{{ route('books.index') }}">
            ← 書籍一覧へ戻る
        </a>
    </p>

    <section class="panel book-detail">
        <div class="book-detail-image">
            @if ($book->image_url)
                <img
                    src="{{ $book->image_url }}"
                    alt="{{ $book->title }}"
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
        </div>

        <div class="book-detail-content">
            <div class="book-detail-heading">
                <div>
                    <h1>{{ $book->title }}</h1>
                    <p class="book-author">
                        {{ $book->author }}
                    </p>
                </div>

                @auth
                    @php
                        $isFavorite = auth()
                            ->user()
                            ->favoriteBooks()
                            ->where('books.id', $book->id)
                            ->exists();
                    @endphp

                    @if ($isFavorite)
                        <form
                            method="POST"
                            action="{{ route(
                                'favorites.destroy',
                                $book
                            ) }}"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="favorite-button is-favorite"
                                title="お気に入りから解除"
                            >
                                ♥
                            </button>
                        </form>
                    @else
                        <form
                            method="POST"
                            action="{{ route(
                                'favorites.store',
                                $book
                            ) }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="favorite-button"
                                title="お気に入りに追加"
                            >
                                ♡
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

            <dl class="book-information">
                <div>
                    <dt>ISBN</dt>
                    <dd>{{ $book->isbn ?: '未登録' }}</dd>
                </div>

                <div>
                    <dt>出版日</dt>
                    <dd>
                        {{ $book->published_date
                            ? $book->published_date->format('Y年m月d日')
                            : '未登録' }}
                    </dd>
                </div>

                <div>
                    <dt>平均評価</dt>
                    <dd>
                        <span class="rating">★</span>
                        {{ number_format(
                            $book->reviews_avg_rating ?? 0,
                            1
                        ) }}
                        / 5
                        （{{ $book->reviews_count }}件）
                    </dd>
                </div>

                <div>
                    <dt>登録者</dt>
                    <dd>{{ $book->user->name }}</dd>
                </div>
            </dl>

            <div class="tag-list">
                @forelse ($book->genres as $genre)
                    <span class="tag">{{ $genre->name }}</span>
                @empty
                    <span class="book-meta">ジャンル未設定</span>
                @endforelse
            </div>

            <div class="actions">
                @can('update', $book)
                    <a
                        class="button button-secondary"
                        href="{{ route('books.edit', $book) }}"
                    >
                        編集
                    </a>
                @endcan

                @can('delete', $book)
                    <form
                        method="POST"
                        action="{{ route('books.destroy', $book) }}"
                        onsubmit="
                            return confirm(
                                '本当にこの書籍を削除しますか？'
                            );
                        "
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="button-danger"
                        >
                            削除
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </section>

    <section class="panel">
        <h2 class="panel-title">説明</h2>

        <p class="book-description">
            {{ $book->description ?? '説明はありません。' }}
        </p>
    </section>

    @auth
        @php
            $hasReviewed = $book->reviews->contains(
                'user_id',
                auth()->id()
            );
        @endphp

        @if (!$hasReviewed)
            <section class="panel">
                <h2 class="panel-title">レビューを投稿する</h2>

                @error('review')
                    <p class="error-message">{{ $message }}</p>
                @enderror

                <form
                    method="POST"
                    action="{{ route('reviews.store', $book) }}"
                    class="review-form"
                >
                    @csrf

                    <div class="form-group review-rating-field">
                        <label for="rating">評価</label>

                        <select id="rating" name="rating">
                            <option value="">選択してください</option>

                            @for ($i = 1; $i <= 5; $i++)
                                <option
                                    value="{{ $i }}"
                                    @selected(old('rating') == $i)
                                >
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>

                        @error('rating')
                            <p class="error-message">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="comment">レビュー本文</label>

                        <textarea
                            id="comment"
                            name="comment"
                        >{{ old('comment') }}</textarea>

                        @error('comment')
                            <p class="error-message">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="actions form-actions">
                        <button type="submit">
                            投稿する
                        </button>
                    </div>
                </form>
            </section>
        @else
            <section class="panel">
                <p class="book-meta">
                    この書籍にはすでにレビューを投稿しています。
                </p>
            </section>
        @endif
    @endauth

    <section class="panel">
        <div class="panel-heading">
            <h2 class="panel-title">レビュー</h2>
            <span class="book-meta">
                {{ $book->reviews_count }}件
            </span>
        </div>

        <div class="review-list">
            @forelse ($book->reviews as $review)
                <article class="review-card">
                    <div class="review-heading">
                        <div>
                            <strong>{{ $review->user->name }}</strong>
                            <span class="review-date">
                                {{ $review->created_at->format(
                                    'Y年m月d日'
                                ) }}
                            </span>
                        </div>

                        <span class="rating">
                            ★ {{ number_format($review->rating, 1) }}
                        </span>
                    </div>

                    <p class="review-comment">
                        {{ $review->comment }}
                    </p>

                    <div class="review-footer">
                        <span class="book-meta">
                            いいね：{{ $review->likedByUsers->count() }}件
                        </span>

                        <div class="actions">
                            @auth
                                @if ($review->user_id !== auth()->id())
                                    @php
                                        $hasLiked = $review
                                            ->likedByUsers
                                            ->contains('id', auth()->id());
                                    @endphp

                                    @if ($hasLiked)
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'review-likes.destroy',
                                                $review
                                            ) }}"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="button-secondary"
                                            >
                                                いいね解除
                                            </button>
                                        </form>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'review-likes.store',
                                                $review
                                            ) }}"
                                        >
                                            @csrf

                                            <button type="submit">
                                                いいね
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @endauth

                            @can('update', $review)
                                <a
                                    class="button button-secondary"
                                    href="{{ route(
                                        'reviews.edit',
                                        $review
                                    ) }}"
                                >
                                    編集
                                </a>
                            @endcan

                            @can('delete', $review)
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'reviews.destroy',
                                        $review
                                    ) }}"
                                    onsubmit="
                                        return confirm(
                                            '本当にこのレビューを削除しますか？'
                                        );
                                    "
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="button-danger"
                                    >
                                        削除
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </article>
            @empty
                <p class="empty-text">
                    レビューはまだありません。
                </p>
            @endforelse
        </div>
    </section>
@endsection