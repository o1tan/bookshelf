<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍ランキング | BookShelf</title>
</head>
<body>

    <p>
        <a href="{{ route('books.index') }}">
            ← 書籍一覧へ戻る
        </a>
    </p>

    <h1>書籍ランキング</h1>

    <p>
        レビューの平均評価が高い書籍から表示しています。
    </p>

    @forelse ($books as $book)
        <div>
            <h2>
                第{{ $loop->iteration }}位
            </h2>

            <h3>
                <a href="{{ route('books.show', $book) }}">
                    {{ $book->title }}
                </a>
            </h3>

            <p>
                著者：{{ $book->author }}
            </p>

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
                {{ number_format($book->reviews_avg_rating, 1) }}
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
            ランキング対象の書籍はありません。
        </p>
    @endforelse

</body>
</html>