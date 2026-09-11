@extends('layouts.app')

@section('title', 'ジャンル編集 | BookShelf')

@section('content')
    <div class="page-heading form-page-heading">
        <div>
            <h1>ジャンル編集</h1>
            <p class="form-help">
                ジャンル名を修正してください。
            </p>
        </div>
    </div>

    <section class="panel form-card">
        <form
            method="POST"
            action="{{ route('genres.update', $genre) }}"
            class="form-stack"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">ジャンル名</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $genre->name) }}"
                    maxlength="100"
                    required
                    autofocus
                >

                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="actions form-actions">
                <a
                    class="button button-secondary"
                    href="{{ route('genres.index') }}"
                >
                    キャンセル
                </a>

                <button type="submit">
                    更新する
                </button>
            </div>
        </form>
    </section>
@endsection