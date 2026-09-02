<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;
use App\Http\Requests\UpdateReviewRequest;

class ReviewController extends Controller
{

    public function store(StoreReviewRequest $request, Book $book)
    {
        if (Review::where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->exists()) {

            return redirect()
                ->route('books.show', $book)
                ->withErrors([
                    'review' => 'この書籍にはすでにレビューを投稿しています。',
                ]);
        }

        Review::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'レビューを投稿しました。');
    }

    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()
            ->route('books.show', $review->book_id)
            ->with('success', 'レビューを更新しました。');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $book = $review->book;

        $review->delete();

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'レビューを削除しました。');
    }

}
