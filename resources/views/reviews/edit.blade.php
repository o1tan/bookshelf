@extends('layouts.app')

@section('title', 'レビュー編集 | BookShelf')

@section('content')
    <div class="page-heading form-page-heading">
        <div>
            <h1>レビュー編集</h1>
            <p class="form-help">
                {{ $review->book->title }}
            </p>
        </div>
    </div>

    <section class="panel form-card">
        <form
            method="POST"
            action="{{ route('reviews.update', $review) }}"
            class="form-stack"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="rating">評価</label>

                <select id="rating" name="rating" required>
                    @for ($i = 1; $i <= 5; $i++)
                        <option
                            value="{{ $i }}"
                            @selected(
                                old('rating', $review->rating) == $i
                            )
                        >
                            {{ $i }} / 5
                        </option>
                    @endfor
                </select>

                @error('rating')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="comment">レビュー本文</label>

                <textarea
                    id="comment"
                    name="comment"
                    required
                >{{ old('comment', $review->comment) }}</textarea>

                @error('comment')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="actions form-actions">
                <a
                    class="button button-secondary"
                    href="{{ route('books.show', $review->book) }}"
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