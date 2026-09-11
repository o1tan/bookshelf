@extends('layouts.app')

@section('title', $genre->name . 'の書籍 | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>{{ $genre->name }}の書籍一覧</h1>
            <p class="form-help">
                該当書籍：{{ $books->count() }}冊
            </p>
        </div>

        <a
            class="button button-secondary"
            href="{{ route('genres.index') }}"
        >
            ジャンル管理へ戻る
        </a>
    </div>

    @if ($books->isNotEmpty())
        <div class="book-grid">
            @foreach ($books as $book)
                <article class="book-card">
                    <a href="{{ route('books.show', $book) }}">
                        <div class="book-image">
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
                        </div>
                    </a>

                    <div class="book-content">
                        <h2 class="book-title">
                            <a href="{{ route(
                                'books.show',
                                $book
                            ) }}">
                                {{ $book->title }}
                            </a>
                        </h2>

                        <p class="book-meta">
                            著者：{{ $book->author }}
                        </p>

                        <div class="tag-list">
                            @foreach ($book->genres as $bookGenre)
                                <span class="tag">
                                    {{ $bookGenre->name }}
                                </span>
                            @endforeach
                        </div>

                        <p class="book-meta">
                            <span class="rating">★</span>
                            {{ number_format(
                                $book->reviews_avg_rating ?? 0,
                                1
                            ) }}
                            / 5
                            （{{ $book->reviews_count }}件）
                        </p>

                        <p class="book-meta">
                            登録者：{{ $book->user->name }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <section class="panel empty-state">
            このジャンルに紐づく書籍はありません。
        </section>
    @endif
@endsection