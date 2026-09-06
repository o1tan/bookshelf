<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ジャンル編集 | BookShelf</title>
</head>
<body>

    <p>
        <a href="{{ route('genres.index') }}">
            ← ジャンル管理へ戻る
        </a>
    </p>

    <h1>ジャンル編集</h1>

    <form
        method="POST"
        action="{{ route('genres.update', $genre) }}"
    >
        @csrf
        @method('PUT')

        <div>
            <label for="name">ジャンル名</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $genre->name) }}"
                maxlength="100"
            >

            @error('name')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">
            更新する
        </button>
    </form>

</body>
</html>