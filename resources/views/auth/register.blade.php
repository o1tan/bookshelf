<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録 | BookShelf</title>
</head>
<body>
    <h1>会員登録</h1>

    <form method="POST" action="/register">
        @csrf

        <div>
            <label for="name">名前</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
            >

            @error('name')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
            >

            @error('email')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password">パスワード</label>
            <input
                id="password"
                type="password"
                name="password"
                required
            >

            @error('password')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">パスワード確認</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
            >
        </div>

        <button type="submit">登録</button>
    </form>

    <p>
        すでにアカウントをお持ちの方は
        <a href="/login">ログイン</a>
    </p>
</body>
</html>