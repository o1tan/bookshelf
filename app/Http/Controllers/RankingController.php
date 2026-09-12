<?php

namespace App\Http\Controllers;

use App\Models\Book;

class RankingController extends Controller
{
    public function __invoke()
    {
        // 評価が高い書籍 TOP10
        $ratedBooks = Book::with(['user', 'genres'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->has('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->orderBy('id')
            ->take(10)
            ->get();

        // お気に入りが多い書籍 TOP10
        $favoriteBooks = Book::with(['user', 'genres'])
            ->withCount('favoritedByUsers as favorites_count')
            ->has('favoritedByUsers')
            ->orderByDesc('favorites_count')
            ->orderBy('id')
            ->take(10)
            ->get();

        return view('rankings.index', compact('ratedBooks', 'favoriteBooks'));
    }
}