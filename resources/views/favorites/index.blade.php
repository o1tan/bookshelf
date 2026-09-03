<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入り | BookShelf</title>
</head>
<body>

    <p>
        <a href="{{ route('books.index') }}">
            ← 書籍一覧へ戻る
        </a>
    </p>

    <h1>お気に入り書籍</h1>

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
                登録者：{{ $book->user->name }}
            </p>

            <form
                method="POST"
                action="{{ route('favorites.destroy', $book) }}"
            >
                @csrf
                @method('DELETE')

                <button type="submit">
                    お気に入りから解除
                </button>
            </form>

        </div>

        <hr>

    @empty
        <p>
            お気に入りの書籍はありません。
        </p>
    @endforelse

</body>
</html>