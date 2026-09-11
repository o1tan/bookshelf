<?php

namespace App\Http\Controllers;

use App\Services\GoogleBooksService;
use Illuminate\Http\JsonResponse;
use Throwable;

class IsbnLookupController extends Controller
{
    public function __invoke(
        string $isbn,
        GoogleBooksService $googleBooks
    ): JsonResponse {
        if (! preg_match('/^\d{13}$/', $isbn)) {
            return response()->json([
                'message' => 'ISBNは13桁の数字で入力してください。',
            ], 422);
        }

        try {
            $book = $googleBooks->findByIsbn($isbn);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => '書籍情報の取得に失敗しました。',
            ], 502);
        }

        if (! $book) {
            return response()->json([
                'message' => '該当する書籍が見つかりませんでした。',
            ], 404);
        }

        return response()->json([
            'data' => $book,
        ]);
    }
}
