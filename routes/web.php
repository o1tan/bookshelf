<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewLikeController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\IsbnLookupController;
use App\Http\Controllers\ReadingReportController;

Route::get('/', [BookController::class, 'index']);

Route::get('/books', [BookController::class, 'index'])
    ->name('books.index');

Route::get('/books/export', [BookController::class, 'export'])
    ->name('books.export');

Route::get('/books/isbn/{isbn}', IsbnLookupController::class)
    ->middleware('auth')
    ->name('books.isbn');

Route::get('/books/create', [BookController::class, 'create'])
    ->middleware('auth')
    ->name('books.create');

Route::post('/books', [BookController::class, 'store'])
    ->middleware('auth')
    ->name('books.store');

Route::get('/books/{book}', [BookController::class, 'show'])
    ->name('books.show');

Route::get('/books/{book}/edit', [BookController::class, 'edit'])
    ->middleware('auth')
    ->name('books.edit');

Route::put('/books/{book}', [BookController::class, 'update'])
    ->middleware('auth')
    ->name('books.update');

Route::delete('/books/{book}', [BookController::class, 'destroy'])
    ->middleware('auth')
    ->name('books.destroy');

Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])
    ->middleware('auth')
    ->name('reviews.store');

Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])
    ->middleware('auth')
    ->name('reviews.edit');

Route::put('/reviews/{review}', [ReviewController::class, 'update'])
    ->middleware('auth')
    ->name('reviews.update');

Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
    ->middleware('auth')
    ->name('reviews.destroy');

Route::post('/books/{book}/favorite', [FavoriteController::class, 'store'])
    ->middleware('auth')
    ->name('favorites.store');

Route::delete('/books/{book}/favorite', [FavoriteController::class, 'destroy'])
    ->middleware('auth')
    ->name('favorites.destroy');

Route::get('/favorites', [FavoriteController::class, 'index'])
    ->middleware('auth')
    ->name('favorites.index');

Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'store'])
    ->middleware('auth')
    ->name('review-likes.store');

Route::delete('/reviews/{review}/like', [ReviewLikeController::class, 'destroy'])
    ->middleware('auth')
    ->name('review-likes.destroy');

Route::get('/genres', [GenreController::class, 'index'])
    ->middleware('auth')
    ->name('genres.index');

Route::get('/genres/{genre}', [GenreController::class, 'show'])
    ->middleware('auth')
    ->name('genres.show');

Route::post('/genres', [GenreController::class, 'store'])
    ->middleware('auth')
    ->name('genres.store');

Route::get('/genres/{genre}/edit', [GenreController::class, 'edit'])
    ->middleware('auth')
    ->name('genres.edit');

Route::put('/genres/{genre}', [GenreController::class, 'update'])
    ->middleware('auth')
    ->name('genres.update');

Route::delete('/genres/{genre}', [GenreController::class, 'destroy'])
    ->middleware('auth')
    ->name('genres.destroy');

Route::get('/rankings', RankingController::class)
    ->name('rankings.index');

Route::get('/reading-report', ReadingReportController::class)
    ->middleware('auth')->name('reading-report');


