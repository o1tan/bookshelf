@extends('layouts.app')

@section('title', 'お気に入り書籍 | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>お気に入り書籍</h1>
            <p class="form-help">
                お気に入りに登録した書籍の一覧です。
            </p>
        </div>
    </div>

    @if ($books->isNotEmpty())
        <div class="book-grid">
            @foreach ($books as $book)
                <article class="book-card favorite-card">
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
                        <div class="favorite-card-heading">
                            <h2 class="book-title">
                                <a href="{{ route(
                                    'books.show',
                                    $book
                                ) }}">
                                    {{ $book->title }}
                                </a>
                            </h2>

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
                        </div>

                        <p class="book-meta">
                            著者：{{ $book->author }}
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
            お気に入りの書籍はありません。
        </section>
    @endif
@endsection