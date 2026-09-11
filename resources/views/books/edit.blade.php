@extends('layouts.app')

@section('title', '書籍編集 | BookShelf')

@section('content')
    <div class="page-heading form-page-heading">
        <div>
            <h1>書籍編集</h1>
            <p class="form-help">
                書籍情報を修正してください。
            </p>
        </div>
    </div>

    <section class="panel form-card">
        <form
            method="POST"
            action="{{ route('books.update', $book) }}"
            class="form-stack"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">タイトル</label>

                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $book->title) }}"
                    required
                >

                @error('title')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="author">著者</label>

                <input
                    id="author"
                    type="text"
                    name="author"
                    value="{{ old('author', $book->author) }}"
                    required
                >

                @error('author')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="isbn">ISBN</label>

                <input
                    id="isbn"
                    type="text"
                    name="isbn"
                    value="{{ old('isbn', $book->isbn) }}"
                >

                @error('isbn')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="published_date">出版日</label>

                <input
                    id="published_date"
                    type="date"
                    name="published_date"
                    value="{{ old(
                        'published_date',
                        $book->published_date?->format('Y-m-d')
                    ) }}"
                >

                @error('published_date')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">説明</label>

                <textarea
                    id="description"
                    name="description"
                >{{ old('description', $book->description) }}</textarea>

                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="image_url">画像URL</label>

                <input
                    id="image_url"
                    type="url"
                    name="image_url"
                    value="{{ old('image_url', $book->image_url) }}"
                >

                @error('image_url')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <fieldset class="genre-fieldset">
                <legend class="form-label">ジャンル</legend>

                <div class="genre-options">
                    @foreach ($genres as $genre)
                        <label>
                            <input
                                type="checkbox"
                                name="genres[]"
                                value="{{ $genre->id }}"
                                @checked(in_array(
                                    $genre->id,
                                    old(
                                        'genres',
                                        $book->genres
                                            ->pluck('id')
                                            ->toArray()
                                    )
                                ))
                            >
                            {{ $genre->name }}
                        </label>
                    @endforeach
                </div>

                @error('genres')
                    <p class="error-message">{{ $message }}</p>
                @enderror

                @error('genres.*')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="actions form-actions">
                <a
                    class="button button-secondary"
                    href="{{ route('books.show', $book) }}"
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