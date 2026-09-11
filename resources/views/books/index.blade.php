<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍一覧 | BookShelf</title>
</head>
<body>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif
    
    <h1>BookShelf</h1>
    <h2>書籍一覧</h2>

    <form method="GET" action="{{ route('books.index') }}">
        <div>
            <label for="keyword">キーワード</label>
            <input
                id="keyword"
                type="search"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="タイトル・著者名で検索"
            >
        </div>

        <div>
            <label for="genre">ジャンル</label>
            <select id="genre" name="genre">
                <option value="">すべて</option>

                @foreach ($genres as $genreOption)
                    <option
                        value="{{ $genreOption->id }}"
                        @selected((string) $genre === (string) $genreOption->id)
                    >
                        {{ $genreOption->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="sort">並び順</label>
            <select id="sort" name="sort">
                <option value="latest" @selected($sort === 'latest')>
                    新着順
                </option>
                <option value="oldest" @selected($sort === 'oldest')>
                    古い順
                </option>
                <option value="title" @selected($sort === 'title')>
                    タイトル順
                </option>
                <option value="rating" @selected($sort === 'rating')>
                    評価が高い順
                </option>
            </select>
        </div>

        <button type="submit">検索する</button>

        <a href="{{ route('books.index') }}">
            条件をクリア
        </a>
    </form>

    <p>
        <a href="{{ route('rankings.index') }}">
            書籍ランキングを見る
        </a>
    </p>

    @auth
        <p>
            <a href="{{ route('favorites.index') }}">
                お気に入り書籍を見る
            </a>
        </p>

        <p>
            <a href="{{ route('genres.index') }}">
                ジャンルを管理する
            </a>
        </p>
    @endauth

    @forelse ($books as $book)
        <div>
            <h3>
                <a href="{{ route('books.show', $book) }}">
                    {{ $book->title }}
                </a>
            </h3>

            <p>著者：{{ $book->author }}</p>

            <p>
                ジャンル：
                @foreach ($book->genres as $genre)
                    {{ $genre->name }}
                    @unless ($loop->last)
                        /
                    @endunless
                @endforeach
            </p>

            <p>
                平均評価：
                {{ number_format($book->reviews_avg_rating ?? 0, 1) }}
                / 5
            </p>

            <p>
                レビュー件数：
                {{ $book->reviews_count }}件
            </p>

            <p>
                登録者：
                {{ $book->user->name }}
            </p>

            <hr>
        </div>
    @empty
        <p>書籍が登録されていません。</p>
    @endforelse

    @if ($books->hasPages())
        <div>
            {{ $books->links() }}
        </div>
    @endif

</body>
</html>