<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'BookShelf')
    </title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('books.index') }}">
                BookShelf
            </a>

            <nav class="navigation">
                <a href="{{ route('books.index') }}">
                    書籍一覧
                </a>

                <a href="{{ route('rankings.index') }}">
                    ランキング
                </a>

                @auth
                    <a href="{{ route('favorites.index') }}">
                        お気に入り
                    </a>

                    <a href="{{ route('genres.index') }}">
                        ジャンル管理
                    </a>

                    @if (Route::has('reports.index'))
                        <a href="{{ route('reports.index') }}">
                            読書レポート
                        </a>
                    @endif

                    @if (Route::has('reading-plans.index'))
                        <a href="{{ route('reading-plans.index') }}">
                            読書計画
                        </a>
                    @endif

                    @if (Route::has('notifications.index'))
                        <a href="{{ route('notifications.index') }}">
                            通知
                        </a>
                    @endif
                @endauth
            </nav>

            <div class="user-area">
                @auth
                    <span>{{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button class="logout-button" type="submit">
                            ログアウト
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}">
                        ログイン
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="page-container">
        @if (session('success'))
            <div class="flash-message">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>