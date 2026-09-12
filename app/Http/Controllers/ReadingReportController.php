<?php

namespace App\Http\Controllers;

use App\Models\ReadingPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReadingReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $reviews = $user->reviews()
            ->with('book.genres')
            ->get();

        $reviewCount = $reviews->count();
        $completedBookCount = $user->readingPlans()
            ->where('status', ReadingPlan::STATUS_COMPLETED)
            ->distinct('book_id')
            ->count('book_id');

        $averageRating = $reviews->avg('rating');

        $ratingDistribution = collect(range(1, 5))
            ->mapWithKeys(fn (int $rating) => [
                $rating => $reviews->where('rating', $rating)->count(),
            ]);

        $highRatedReviews = $reviews
            ->filter(fn ($review) => $review->rating >= 4)
            ->sortByDesc('rating')
            ->take(5)
            ->values();

        $genreRatingTrends = $reviews
            ->flatMap(fn ($review) => $review->book->genres->map(
                fn ($genre) => [
                    'genre' => $genre,
                    'rating' => $review->rating,
                ]
            ))
            ->groupBy(fn (array $item) => $item['genre']->id)
            ->map(fn ($items) => [
                'genre' => $items->first()['genre'],
                'average_rating' => $items->avg('rating'),
                'count' => $items->count(),
            ])
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

        return view('reading-reports.show', compact(
            'reviewCount',
            'completedBookCount',
            'averageRating',
            'ratingDistribution',
            'highRatedReviews',
            'genreRatingTrends'
        ));
    }
}
