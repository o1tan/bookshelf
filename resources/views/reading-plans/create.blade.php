<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>読書計画の登録</title>
</head>
<body>
    <h1>読書計画の登録</h1>

    <p>
        <a href="{{ route('reading-plans.index') }}">
            読書計画一覧へ戻る
        </a>
    </p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        method="POST"
        action="{{ route('reading-plans.store') }}"
    >
        @csrf

        <div>
            <label for="book_id">書籍</label>

            <select id="book_id" name="book_id" required>
                <option value="">選択してください</option>

                @foreach ($books as $book)
                    <option
                        value="{{ $book->id }}"
                        @selected(old('book_id') == $book->id)
                    >
                        {{ $book->title }}／{{ $book->author }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="deadline">読了期限</label>

            <input
                id="deadline"
                name="deadline"
                type="date"
                min="{{ now()->format('Y-m-d') }}"
                value="{{ old('deadline') }}"
                required
            >
        </div>

        <div>
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

        <div>
            <label for="reminder_at">通知日時（任意）</label>

            <input
                id="reminder_at"
                name="reminder_at"
                type="datetime-local"
                value="{{ old('reminder_at') }}"
            >
        </div>

        <button type="submit">
            登録する
        </button>
    </form>
</body>
</html>