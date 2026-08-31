<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index']);

Route::get('/books', [BookController::class, 'index'])
    ->name('books.index');
    
Route::get('/books/create', [BookController::class, 'create'])
    ->middleware('auth')
    ->name('books.create');

Route::post('/books', [BookController::class, 'store'])
    ->middleware('auth')
    ->name('books.store');

Route::get('/books/{book}', [BookController::class, 'show'])
    ->name('books.show');