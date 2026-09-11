@extends('layouts.app')

@section('title', '書籍一覧 | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>書籍一覧</h1>
            <p class="form-help">
                登録された書籍を検索・絞り込みできます。
            </p>
        </div>

        @auth
            <a class="button" href="{{ route('books.create') }}">
                書籍を登録
            </a>
        @endauth
    </div>

    <section class="panel">
        <form
            method="GET"
            action="{{ route('books.index') }}"
            class="search-grid"
        >
            <div class="form-group">
                <label for="keyword">キーワード</label>
                <input
                    id="keyword"
                    type="search"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="タイトル・著者名で検索"
                >
            </div>

            <div class="form-group">
                <label for="genre">ジャンル</label>
                <select id="genre" name="genre">
                    <option value="">すべて</option>

                    @foreach ($genres as $genreOption)
                        <option
                            value="{{ $genreOption->id }}"
                            @selected(
                                (string) $genre ===
                                (string) $genreOption->id
                            )
                        >
                            {{ $genreOption->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="sort">並び順</label>
                <select id="sort" name="sort">
                    <option
                        value="latest"
                        @selected($sort === 'latest')
                    >
                        新着順
                    </option>
                    <option
                        value="oldest"
                        @selected($sort === 'oldest')
                    >
                        古い順
                    </option>
                    <option
                        value="title"
                        @selected($sort === 'title')
                    >
                        タイトル順
                    </option>
                    <option
                        value="rating"
                        @selected($sort === 'rating')
                    >
                        評価が高い順
                    </option>
                </select>
            </div>

            <button type="submit">
                検索する
            </button>
        </form>

        <div class="actions" style="margin-top: 16px;">
            <a
                class="button button-secondary"
                href="{{ route('books.index') }}"
            >
                条件をクリア
            </a>

            <a
                class="button button-success"
                href="{{ route(
                    'books.export',
                    request()->query()
                ) }}"
            >
                現在の条件でCSV出力
            </a>
        </div>
    </section>

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
                                        this.nextElementSibling.style.display='block';
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
                            <a href="{{ route('books.show', $book) }}">
                                {{ $book->title }}
                            </a>
                        </h2>

                        <p class="book-meta">
                            著者：{{ $book->author }}
                        </p>

                        <div class="tag-list">
                            @forelse ($book->genres as $bookGenre)
                                <span class="tag">
                                    {{ $bookGenre->name }}
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

                <x-pagination :paginator="$books" />
                    @else
                        <div class="panel empty-state">
                            条件に一致する書籍がありません。
                        </div>
                    @endif
                @endsection
