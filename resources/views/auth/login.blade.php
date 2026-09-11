@extends('layouts.guest')

@section('title', 'ログイン | BookShelf')

@section('content')
    <h1 class="auth-title">ログイン</h1>

    @if ($errors->any())
        <div class="error-summary">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('login') }}"
        class="form-stack"
    >
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="email"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>

            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
            >
        </div>

        <label class="remember-field">
            <input
                type="checkbox"
                name="remember"
            >
            <span>ログイン状態を保持する</span>
        </label>

        <button type="submit">
            ログイン
        </button>
    </form>

    <p class="auth-footer">
        アカウントをお持ちでない方は
        <a href="{{ route('register') }}">
            会員登録
        </a>
    </p>
@endsection