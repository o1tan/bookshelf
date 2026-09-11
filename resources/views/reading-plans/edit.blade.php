<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>読書計画の編集</title>
</head>
<body>
    <h1>読書計画の編集</h1>

    <p>
        <a href="{{ route('reading-plans.index') }}">
            読書計画一覧へ戻る
        </a>
    </p>

    <h2>{{ $readingPlan->book->title }}</h2>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        method="POST"
        action="{{ route('reading-plans.update', $readingPlan) }}"
    >
        @csrf
        @method('PUT')

        <div>
            <label for="deadline">読了期限</label>

            <input
                id="deadline"
                name="deadline"
                type="date"
                min="{{ now()->format('Y-m-d') }}"
                value="{{ old(
                    'deadline',
                    $readingPlan->deadline->format('Y-m-d')
                ) }}"
                required
            >
        </div>

        <div>
            <label for="status">読書状態</label>

            <select id="status" name="status" required>
                <option
                    value="not_started"
                    @selected(
                        old('status', $readingPlan->status)
                        === 'not_started'
                    )
                >
                    未読
                </option>

                <option
                    value="reading"
                    @selected(
                        old('status', $readingPlan->status)
                        === 'reading'
                    )
                >
                    読書中
                </option>

                <option
                    value="completed"
                    @selected(
                        old('status', $readingPlan->status)
                        === 'completed'
                    )
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
                value="{{ old(
                    'reminder_at',
                    $readingPlan->reminder_at?->format('Y-m-d\TH:i')
                ) }}"
            >
        </div>

        <button type="submit">
            更新する
        </button>
    </form>

    <form
        method="POST"
        action="{{ route('reading-plans.destroy', $readingPlan) }}"
    >
        @csrf
        @method('DELETE')

        <button type="submit">
            削除する
        </button>
    </form>
</body>
</html>