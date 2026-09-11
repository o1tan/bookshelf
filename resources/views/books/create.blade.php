@extends('layouts.app')

@section('title', '書籍登録 | BookShelf')

@section('content')
    <div class="page-heading">
        <div>
            <h1>書籍の登録</h1>
            <p class="form-help">
                ISBN検索を使うと書籍情報を自動入力できます。
            </p>
        </div>

        <a
            class="button button-secondary"
            href="{{ route('books.index') }}"
        >
            書籍一覧へ戻る
        </a>
    </div>

    <section class="panel form-card">
        <form
            method="POST"
            action="{{ route('books.store') }}"
            class="form-stack"
        >
            @csrf

            <div class="form-group">
                <label for="isbn">ISBN（任意）</label>

                <div class="isbn-row">
                    <input
                        id="isbn"
                        type="text"
                        name="isbn"
                        value="{{ old('isbn') }}"
                        inputmode="numeric"
                        maxlength="13"
                        placeholder="13桁のISBN"
                    >

                    <button
                        id="isbn-search-button"
                        type="button"
                    >
                        ISBN検索
                    </button>
                </div>

                <p
                    id="isbn-search-message"
                    class="form-help"
                ></p>

                @error('isbn')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="title">タイトル</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
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
                    value="{{ old('author') }}"
                >

                @error('author')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="published_date">
                    出版日（任意）
                </label>
                <input
                    id="published_date"
                    type="date"
                    name="published_date"
                    value="{{ old('published_date') }}"
                >

                @error('published_date')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">説明（任意）</label>
                <textarea
                    id="description"
                    name="description"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="image_url">画像URL（任意）</label>
                <input
                    id="image_url"
                    type="url"
                    name="image_url"
                    value="{{ old('image_url') }}"
                >

                @error('image_url')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <span class="form-label">ジャンル</span>

                <div class="genre-options">
                    @foreach ($genres as $genre)
                        <label>
                            <input
                                type="checkbox"
                                name="genres[]"
                                value="{{ $genre->id }}"
                                @checked(
                                    in_array(
                                        $genre->id,
                                        old('genres', [])
                                    )
                                )
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
            </div>

            <div class="actions">
                <button type="submit">
                    登録する
                </button>

                <a
                    class="button button-secondary"
                    href="{{ route('books.index') }}"
                >
                    キャンセル
                </a>
            </div>
        </form>
    </section>
@endsection

@section('scripts')
    <script>
        const isbnInput = document.getElementById('isbn');
        const searchButton = document.getElementById(
            'isbn-search-button'
        );
        const searchMessage = document.getElementById(
            'isbn-search-message'
        );

        searchButton.addEventListener('click', async () => {
            const isbn = isbnInput.value.trim();

            searchMessage.textContent = '';

            if (!/^\d{13}$/.test(isbn)) {
                searchMessage.textContent =
                    'ISBNは13桁の数字で入力してください。';
                return;
            }

            searchButton.disabled = true;
            searchButton.textContent = '検索中...';

            try {
                const response = await fetch(
                    `{{ url('/books/isbn') }}/${isbn}`,
                    {
                        headers: {
                            Accept: 'application/json',
                        },
                    }
                );

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(
                        result.message
                        ?? '書籍情報の取得に失敗しました。'
                    );
                }

                const book = result.data;

                document.getElementById('title').value =
                    book.title ?? '';

                document.getElementById('author').value =
                    book.author ?? '';

                document.getElementById('published_date').value =
                    book.published_date ?? '';

                document.getElementById('description').value =
                    book.description ?? '';

                document.getElementById('image_url').value =
                    book.image_url ?? '';

                isbnInput.value = book.isbn ?? isbn;

                searchMessage.textContent =
                    '書籍情報を取得しました。';
                searchMessage.classList.add('message-success');
            } catch (error) {
                searchMessage.textContent =
                    error.message
                    ?? '書籍情報の取得に失敗しました。';
                searchMessage.classList.remove('message-success');
            } finally {
                searchButton.disabled = false;
                searchButton.textContent = 'ISBN検索';
            }
        });
    </script>
@endsection