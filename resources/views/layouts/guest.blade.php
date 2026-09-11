<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'BookShelf')
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >
</head>
<body>
    <main class="auth-page">
        <a
            class="auth-logo"
            href="{{ route('books.index') }}"
            aria-label="BookShelf トップへ"
        >
            <span class="auth-logo-mark">BS</span>
            <span>BookShelf</span>
        </a>

        <section class="auth-card">
            @yield('content')
        </section>
    </main>
</body>
</html>