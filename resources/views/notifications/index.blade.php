@extends('layouts.app')

@section('title', '通知一覧 | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>通知一覧</h1>
        </div>
    </div>

    @if ($notifications->isNotEmpty())
        <div class="notification-list">
            @foreach ($notifications as $notification)
                <article class="panel notification-card">
                    <div class="notification-icon">
                        🔔
                    </div>

                    <div class="notification-content">
                        <p class="notification-message">
                            {{ $notification->data['message']
                                ?? '読書計画のお知らせです。' }}
                        </p>

                        <p class="book-meta">
                            {{ $notification->created_at->format(
                                'Y年m月d日 H:i'
                            ) }}
                        </p>
                    </div>

                    @if (isset($notification->data['book_id']))
                        <a
                            class="button button-secondary"
                            href="{{ route(
                                'books.show',
                                $notification->data['book_id']
                            ) }}"
                        >
                            書籍を見る
                        </a>
                    @endif
                </article>
            @endforeach
        </div>

            <x-pagination :paginator="$notifications" />
            @else
                <section class="panel empty-state notification-empty">
                    <div class="notification-empty-icon">🔔</div>
                    <p>新しい通知はありません。</p>
                </section>
            @endif
        @endsection