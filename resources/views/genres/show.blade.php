<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $genre->name }}の書籍 | BookShelf</title>
</head>
<body>

    <p>
        <a href="{{ route('genres.index') }}">
            ← ジャンル管理へ戻る
        </a>
    </p>

    <h1>{{ $genre->name }}の書籍一覧</h1>

    <p>
        該当書籍：{{ $books->count() }}冊
    </p>

    @forelse ($books as $book)
        <div>
            <h2>
                <a href="{{ route('books.show', $book) }}">
                    {{ $book->title }}
                </a>
            </h2>

            <p>
                著者：{{ $book->author }}
            </p>

            <p>
                ジャンル：
                @foreach ($book->genres as $bookGenre)
                    {{ $bookGenre->name }}

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
                登録者：{{ $book->user->name }}
            </p>
        </div>

        <hr>

    @empty
        <p>
            このジャンルに紐づく書籍はありません。
        </p>
    @endforelse

</body>
</html>