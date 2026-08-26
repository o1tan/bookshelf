<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍一覧 | BookShelf</title>
</head>
<body>
    <h1>BookShelf</h1>

    <p>ログインしました。</p>
    <p>ようこそ、{{ auth()->user()->name }} さん</p>

    <form method="POST" action="/logout">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
</body>
</html>