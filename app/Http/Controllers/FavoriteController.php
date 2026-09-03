<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{

    public function index(Request $request)
    {
        $books = $request->user()
            ->favoriteBooks()
            ->with(['user', 'genres'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest('favorites.created_at')
            ->get();

        return view('favorites.index', compact('books'));
    }

    public function store(Request $request, Book $book)
    {
        $request->user()
            ->favoriteBooks()
            ->syncWithoutDetaching([$book->id]);

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'お気に入りに追加しました。');
    }

    public function destroy(Request $request, Book $book)
    {
        $request->user()
            ->favoriteBooks()
            ->detach($book->id);

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'お気に入りから解除しました。');
    }
}