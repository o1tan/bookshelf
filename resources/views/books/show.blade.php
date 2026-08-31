<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} | BookShelf</title>
</head>
<body>
    <p>
        <a href="{{ route('books.index') }}">← 書籍一覧へ戻る</a>
    </p>

    @can('update', $book)
        <p>
            <a href="{{ route('books.edit', $book) }}">この書籍を編集する</a>
        </p>
    @endcan

    @can('delete', $book)
        <form
            method="POST"
            action="{{ route('books.destroy', $book) }}"
            onsubmit="return confirm('本当にこの書籍を削除しますか？');"
        >
            @csrf
            @method('DELETE')

            <button type="submit">この書籍を削除する</button>
        </form>
    @endcan

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <h1>{{ $book->title }}</h1>

    <p>著者：{{ $book->author }}</p>

    <p>ISBN：{{ $book->isbn }}</p>

    <p>
        出版日：
        {{ $book->published_date->format('Y年m月d日') }}
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

    <p>レビュー件数：{{ $book->reviews_count }}件</p>

    <p>登録者：{{ $book->user->name }}</p>

    <h2>説明</h2>

    <p>
        {{ $book->description ?? '説明はありません。' }}
    </p>

    <hr>

    <h2>レビュー</h2>

    @forelse ($book->reviews as $review)
        <div>
            <p>投稿者：{{ $review->user->name }}</p>
            <p>評価：{{ $review->rating }} / 5</p>
            <p>{{ $review->comment }}</p>
            <p>投稿日：{{ $review->created_at->format('Y年m月d日') }}</p>
        </div>

        <hr>
    @empty
        <p>レビューはまだありません。</p>
    @endforelse
</body>
</html>