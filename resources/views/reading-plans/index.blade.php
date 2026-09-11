@extends('layouts.app')

@section('title', '読書計画 | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>読書計画</h1>
        </div>

        <a
            class="button"
            href="{{ route('reading-plans.create') }}"
        >
            新規計画作成
        </a>
    </div>

    <section class="panel">
        <form
            method="GET"
            action="{{ route('reading-plans.index') }}"
            class="plan-filter"
        >
            <div class="form-group">
                <label for="status">状態</label>

                <select
                    id="status"
                    name="status"
                    onchange="this.form.submit()"
                >
                    <option value="">すべて</option>
                    <option
                        value="not_started"
                        @selected($status === 'not_started')
                    >
                        未読
                    </option>
                    <option
                        value="reading"
                        @selected($status === 'reading')
                    >
                        読書中
                    </option>
                    <option
                        value="completed"
                        @selected($status === 'completed')
                    >
                        読了
                    </option>
                    <option
                        value="expired"
                        @selected($status === 'expired')
                    >
                        期限切れ
                    </option>
                </select>
            </div>
        </form>
    </section>

    @if ($readingPlans->isNotEmpty())
        <div class="plan-list">
            @foreach ($readingPlans as $readingPlan)
                @php
                    $statusLabels = [
                        'not_started' => '未読',
                        'reading' => '読書中',
                        'completed' => '読了',
                        'expired' => '期限切れ',
                    ];
                @endphp

                <article class="panel plan-card">
                    <div>
                        <h2 class="plan-title">
                            <a href="{{ route(
                                'books.show',
                                $readingPlan->book
                            ) }}">
                                {{ $readingPlan->book->title }}
                            </a>
                        </h2>

                        <p class="book-meta">
                            著者：{{ $readingPlan->book->author }}
                        </p>

                        <div class="plan-details">
                            <span class="status-badge status-{{ $readingPlan->status }}">
                                {{ $statusLabels[$readingPlan->status] }}
                            </span>

                            <span>
                                読了期限：
                                {{ $readingPlan->deadline->format('Y年m月d日') }}
                            </span>

                            <span>
                                通知：
                                {{ $readingPlan->reminder_at
                                    ? $readingPlan->reminder_at->format(
                                        'Y年m月d日 H:i'
                                    )
                                    : '設定なし' }}
                            </span>
                        </div>
                    </div>

                    <div class="actions">
                        <a
                            class="button button-secondary"
                            href="{{ route(
                                'reading-plans.edit',
                                $readingPlan
                            ) }}"
                        >
                            編集
                        </a>

                        <form
                            method="POST"
                            action="{{ route(
                                'reading-plans.destroy',
                                $readingPlan
                            ) }}"
                            onsubmit="
                                return confirm(
                                    'この読書計画を削除しますか？'
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
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <section class="panel empty-state">
            表示できる読書計画がありません。
        </section>
    @endif
@endsection