@extends('layouts.guest')

@section('title', '会員登録 | BookShelf')

@section('content')
    <h1 class="auth-title">会員登録</h1>

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
        action="{{ route('register') }}"
        class="form-stack"
    >
        @csrf

        <div class="form-group">
            <label for="name">ユーザー名</label>

            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                autocomplete="name"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="email"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>

            <input
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
            >
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                パスワード確認
            </label>

            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
            >
        </div>

        <button type="submit">
            登録する
        </button>
    </form>

    <p class="auth-footer">
        すでにアカウントをお持ちの方は
        <a href="{{ route('login') }}">
            ログイン
        </a>
    </p>
@endsection