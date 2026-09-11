@extends('layouts.app')

@section('title', '読書計画編集 | BookShelf')

@section('content')
    <div class="page-heading form-page-heading">
        <div>
            <h1>読書計画編集</h1>
            <p class="form-help">
                {{ $readingPlan->book->title }}
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
            action="{{ route(
                'reading-plans.update',
                $readingPlan
            ) }}"
            class="form-stack"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>書籍</label>
                <p class="selected-book">
                    {{ $readingPlan->book->title }}
                </p>
            </div>

            <div class="form-group">
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

                @error('deadline')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
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

            <div class="form-group">
                <label for="reminder_at">
                    通知日時（任意）
                </label>

                <input
                    id="reminder_at"
                    name="reminder_at"
                    type="datetime-local"
                    value="{{ old(
                        'reminder_at',
                        $readingPlan->reminder_at?->format('Y-m-d\TH:i')
                    ) }}"
                >

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
                    更新
                </button>
            </div>
        </form>
    </section>
@endsection