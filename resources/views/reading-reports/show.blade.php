<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>読書レポート</title>
</head>
<body>
    <h1>読書レポート</h1>

    <p>
        <a href="{{ route('books.index') }}">書籍一覧へ戻る</a>
    </p>

    <h2>読書状況</h2>

    <ul>
        <li>未読：{{ $statusCounts['not_started'] }}冊</li>
        <li>読書中：{{ $statusCounts['reading'] }}冊</li>
        <li>読了：{{ $statusCounts['completed'] }}冊</li>
    </ul>

    <h2>レビュー実績</h2>

    <ul>
        <li>投稿数：{{ $reviewCount }}件</li>
        <li>
            平均評価：
            {{ $averageRating !== null ? number_format($averageRating, 1) : '未評価' }}
        </li>
    </ul>

    <h2>登録している読書計画</h2>

    @forelse ($readingPlans as $readingPlan)
        <div>
            <h3>
                <a href="{{ route('books.show', $readingPlan->book) }}">
                    {{ $readingPlan->book->title }}
                </a>
            </h3>

            <p>著者：{{ $readingPlan->book->author }}</p>
            <p>期限：{{ $readingPlan->deadline->format('Y年m月d日') }}</p>

            <p>
                状態：
                @switch($readingPlan->status)
                    @case('not_started')
                        未読
                        @break
                    @case('reading')
                        読書中
                        @break
                    @case('completed')
                        読了
                        @break
                    @default
                        不明
                @endswitch
            </p>
        </div>

        <hr>
    @empty
        <p>登録されている読書計画はありません。</p>
    @endforelse
</body>
</html>