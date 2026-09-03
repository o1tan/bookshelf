<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewLikeController extends Controller
{
    public function store(Request $request, Review $review)
    {
        if ($review->user_id === $request->user()->id) {
            abort(403);
        }

        $request->user()
            ->likedReviews()
            ->syncWithoutDetaching([$review->id]);

        return redirect()
            ->route('books.show', $review->book_id)
            ->with('success', 'レビューにいいねしました。');
    }

    public function destroy(Request $request, Review $review)
    {
        $request->user()
            ->likedReviews()
            ->detach($review->id);

        return redirect()
            ->route('books.show', $review->book_id)
            ->with('success', 'レビューのいいねを解除しました。');
    }
}