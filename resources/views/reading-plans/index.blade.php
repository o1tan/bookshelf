<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>読書計画一覧</title>
</head>
<body>
    <h1>読書計画一覧</h1>

    <p>
        <a href="{{ route('books.index') }}">書籍一覧へ戻る</a>
    </p>

    <p>
        <a href="{{ route('reading-plans.create') }}">
            読書計画を登録する
        </a>
    </p>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @php
        $statusLabels = [
            'not_started' => '未読',
            'reading' => '読書中',
            'completed' => '読了',
        ];
    @endphp

    @forelse ($readingPlans as $readingPlan)
        <div>
            <h2>
                <a href="{{ route('books.show', $readingPlan->book) }}">
                    {{ $readingPlan->book->title }}
                </a>
            </h2>

            <p>
                状態：
                {{ $statusLabels[$readingPlan->status] ?? $readingPlan->status }}
            </p>

            <p>
                期限：{{ $readingPlan->deadline->format('Y年m月d日') }}
            </p>

            <p>
                通知日時：
                {{ $readingPlan->reminder_at?->format('Y年m月d日 H:i') ?? '設定なし' }}
            </p>

            <p>
                <a href="{{ route('reading-plans.edit', $readingPlan) }}">
                    編集する
                </a>
            </p>

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
        </div>

        <hr>
    @empty
        <p>登録されている読書計画はありません。</p>
    @endforelse
</body>
</html>