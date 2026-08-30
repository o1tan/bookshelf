<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍一覧 | BookShelf</title>
</head>
<body>
    <h1>BookShelf</h1>
    <h2>書籍一覧</h2>

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
</body>
</html>