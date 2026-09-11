<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>書籍登録 | BookShelf</title>
</head>
<body>
    <p>
        <a href="{{ route('books.index') }}">← 書籍一覧へ戻る</a>
    </p>

    <h1>書籍登録</h1>

    <form method="POST" action="{{ route('books.store') }}">
        @csrf

        <div>
            <label for="title">タイトル</label>
            <input
                id="title"
                type="text"
                name="title"
                value="{{ old('title') }}"
            >

            @error('title')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="author">著者</label>
            <input
                id="author"
                type="text"
                name="author"
                value="{{ old('author') }}"
            >

            @error('author')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="isbn">ISBN</label>
            <input
                id="isbn"
                type="text"
                name="isbn"
                value="{{ old('isbn') }}"
                inputmode="numeric"
                maxlength="13"
                placeholder="13桁のISBN"
            >

            <button id="isbn-search-button" type="button">
                ISBN検索
            </button>

            <p id="isbn-search-message"></p>

            @error('isbn')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="published_date">出版日</label>
            <input
                id="published_date"
                type="date"
                name="published_date"
                value="{{ old('published_date') }}"
            >

            @error('published_date')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description">説明</label>
            <textarea
                id="description"
                name="description"
            >{{ old('description') }}</textarea>

            @error('description')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="image_url">画像URL</label>
            <input
                id="image_url"
                type="url"
                name="image_url"
                value="{{ old('image_url') }}"
            >

            @error('image_url')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <p>ジャンル</p>

            @foreach ($genres as $genre)
                <label>
                    <input
                        type="checkbox"
                        name="genres[]"
                        value="{{ $genre->id }}"
                        {{ in_array($genre->id, old('genres', [])) ? 'checked' : '' }}
                    >
                    {{ $genre->name }}
                </label>
            @endforeach

            @error('genres')
                <p>{{ $message }}</p>
            @enderror

            @error('genres.*')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">登録する</button>
    </form>

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
            } catch (error) {
                searchMessage.textContent = error.message;
            } finally {
                searchButton.disabled = false;
                searchButton.textContent = 'ISBN検索';
            }
        });
    </script>
</body>
</html>