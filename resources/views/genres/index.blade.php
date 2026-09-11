@extends('layouts.app')

@section('title', 'ジャンル管理 | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>ジャンル管理</h1>
            <p class="form-help">
                書籍に設定するジャンルを管理します。
            </p>
        </div>
    </div>

    <section class="panel">
        <h2 class="panel-title">ジャンル登録</h2>

        <form
            method="POST"
            action="{{ route('genres.store') }}"
            class="genre-create-form"
        >
            @csrf

            <div class="form-group">
                <label for="name">ジャンル名</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    placeholder="例：ミステリー"
                    required
                >

                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">
                登録する
            </button>
        </form>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <h2 class="panel-title">ジャンル一覧</h2>

            <span class="book-meta">
                {{ $genres->count() }}件
            </span>
        </div>

        <div class="genre-list">
            @forelse ($genres as $genre)
                <article class="genre-row">
                    <div>
                        <h3>{{ $genre->name }}</h3>

                        <p class="book-meta">
                            使用書籍：{{ $genre->books_count }}冊
                        </p>
                    </div>

                    <div class="actions">
                        <a
                            class="button button-secondary"
                            href="{{ route('genres.show', $genre) }}"
                        >
                            書籍を見る
                        </a>

                        <a
                            class="button button-secondary"
                            href="{{ route('genres.edit', $genre) }}"
                        >
                            編集
                        </a>

                        @if ($genre->books_count === 0)
                            <form
                                method="POST"
                                action="{{ route(
                                    'genres.destroy',
                                    $genre
                                ) }}"
                                onsubmit="
                                    return confirm(
                                        'このジャンルを削除しますか？'
                                    );
                                "
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="button-danger"
                                >
                                    削除
                                </button>
                            </form>
                        @else
                            <span class="genre-in-use">
                                使用中
                            </span>
                        @endif
                    </div>
                </article>
            @empty
                <p class="empty-text">
                    ジャンルは登録されていません。
                </p>
            @endforelse
        </div>
    </section>
@endsection