<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レビュー編集 | BookShelf</title>
</head>
<body>

    <h1>レビュー編集</h1>

    <form
        method="POST"
        action="{{ route('reviews.update', $review) }}"
    >
        @csrf
        @method('PUT')

        <div>
            <label for="rating">評価</label>

            <select id="rating" name="rating">
                @for ($i = 1; $i <= 5; $i++)
                    <option
                        value="{{ $i }}"
                        {{ old('rating', $review->rating) == $i ? 'selected' : '' }}
                    >
                        {{ $i }}
                    </option>
                @endfor
            </select>

            @error('rating')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="comment">レビュー本文</label>

            <textarea
                id="comment"
                name="comment"
            >{{ old('comment', $review->comment) }}</textarea>

            @error('comment')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">レビューを更新する</button>
    </form>

</body>
</html>