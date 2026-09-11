@extends('layouts.app')

@section('title', '新規読書計画作成 | BookShelf')

@section('content')
    <div class="page-heading form-page-heading">
        <div>
            <h1>新規読書計画作成</h1>
            <p class="form-help">
                書籍と読了期限を設定してください。
            </p>
        </div>
    </div>

    <section class="panel form-card">
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
            action="{{ route('reading-plans.store') }}"
            class="form-stack"
        >
            @csrf

            <div class="form-group">
                <label for="book_id">書籍</label>

                <select id="book_id" name="book_id" required>
                    <option value="">書籍を選択</option>

                    @foreach ($books as $book)
                        <option
                            value="{{ $book->id }}"
                            @selected(old('book_id') == $book->id)
                        >
                            {{ $book->title }}／{{ $book->author }}
                        </option>
                    @endforeach
                </select>

                @error('book_id')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="deadline">読了期限</label>

                <input
                    id="deadline"
                    name="deadline"
                    type="date"
                    min="{{ now()->format('Y-m-d') }}"
                    value="{{ old('deadline') }}"
                    required
                >

                @error('deadline')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">読書状態</label>

                <select id="status" name="status" required>
                    <option
                        value="not_started"
                        @selected(old('status') === 'not_started')
                    >
                        未読
                    </option>
                    <option
                        value="reading"
                        @selected(old('status') === 'reading')
                    >
                        読書中
                    </option>
                    <option
                        value="completed"
                        @selected(old('status') === 'completed')
                    >
                        読了
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="reminder_at">
                    通知日時（任意）
                </label>

                <input
                    id="reminder_at"
                    name="reminder_at"
                    type="datetime-local"
                    value="{{ old('reminder_at') }}"
                >

                <p class="form-help">
                    設定した日時以降の日次処理で通知します。
                </p>

                @error('reminder_at')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="actions form-actions">
                <a
                    class="button button-secondary"
                    href="{{ route('reading-plans.index') }}"
                >
                    キャンセル
                </a>

                <button type="submit">
                    登録
                </button>
            </div>
        </form>
    </section>
@endsection