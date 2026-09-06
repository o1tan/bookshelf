<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ジャンル管理 | BookShelf</title>
</head>
<body>

    <p>
        <a href="{{ route('books.index') }}">
            ← 書籍一覧へ戻る
        </a>
    </p>

    <h1>ジャンル管理</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif

    <h2>ジャンル登録</h2>

    <form method="POST" action="{{ route('genres.store') }}">
        @csrf

        <div>
            <label for="name">ジャンル名</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                maxlength="100"
            >

            @error('name')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">
            登録する
        </button>
    </form>

    <hr>

    <h2>ジャンル一覧</h2>

    @forelse ($genres as $genre)
        <div>
            <p>
                {{ $genre->name }}
                （使用書籍：{{ $genre->books_count }}冊）
            </p>

            <p>
                <a href="{{ route('genres.edit', $genre) }}">
                    編集
                </a>
            </p>

            @if ($genre->books_count === 0)
                <form
                    method="POST"
                    action="{{ route('genres.destroy', $genre) }}"
                    onsubmit="return confirm('このジャンルを削除しますか？');"
                >
                    @csrf
                    @method('DELETE')

                    <button type="submit">
                        削除
                    </button>
                </form>
            @else
                <p>
                    使用中のため削除できません。
                </p>
            @endif
        </div>

        <hr>

    @empty
        <p>
            ジャンルはまだ登録されていません。
        </p>
    @endforelse

</body>
</html>