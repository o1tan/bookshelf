<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{

    public function store(StoreReviewRequest $request, Book $book)
    {
        $review = Review::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'レビューを投稿しました。');
    }

}
