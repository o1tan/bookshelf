<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexApiBookRequest;
use App\Http\Requests\StoreApiBookRequest;
use App\Http\Requests\UpdateApiBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(IndexApiBookRequest $request)
    {
        $perPage = max(
            1,
            min($request->integer('per_page', 20), 100)
        );

        $query = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($request->filled('keyword')) {
            $keyword = trim($request->string('keyword')->toString());

            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('genre_id')) {
            $genreId = $request->integer('genre_id');

            $query->whereHas('genres', function ($query) use ($genreId) {
                $query->where('genres.id', $genreId);
            });
        }

        $books = $query->latest()
            ->paginate($perPage)
            ->withQueryString();

        return BookResource::collection($books);
    }

    public function store(StoreApiBookRequest $request)
    {
        $validated = $request->validated();

        $genreIds = $validated['genre_ids'];
        unset($validated['genre_ids']);

        $user = $request->user();

        $book = DB::transaction(function () use ($validated, $genreIds, $user) {
            $book = $user->books()->create($validated);

            $book->genres()->attach($genreIds);

            return $book;
        });

        $book->load(['user', 'genres']);
        $book->loadAvg('reviews', 'rating');
        $book->loadCount('reviews');

        return (new BookResource($book))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Book $book)
    {
        $book->load([
            'user',
            'genres',
            'reviews' => fn ($query) => $query->latest(),
            'reviews.user',
        ]);

        $book->loadAvg('reviews', 'rating');
        $book->loadCount('reviews');

        return new BookResource($book);
    }

    public function update(
        UpdateApiBookRequest $request,
        Book $book
    ) {
        $validated = $request->validated();

        $genreIds = $validated['genre_ids'];
        unset($validated['genre_ids']);

        DB::transaction(function () use ($book, $validated, $genreIds) {
            $book->update($validated);

            $book->genres()->sync($genreIds);
        });

        $book->load(['user', 'genres']);
        $book->loadAvg('reviews', 'rating');
        $book->loadCount('reviews');

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return response()->noContent();
    }
}
