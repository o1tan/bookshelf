<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    public function findByIsbn(string $isbn): ?array
    {
        $parameters = [
            'q' => "isbn:{$isbn}",
            'maxResults' => 1,
        ];

        $apiKey = config('services.google_books.key');

        if ($apiKey) {
            $parameters['key'] = $apiKey;
        }

        $response = Http::acceptJson()
            ->timeout(10)
            ->get(
                config('services.google_books.url'),
                $parameters
            );

        $response->throw();

        $volumeInfo = $response->json('items.0.volumeInfo');

        if (! $volumeInfo) {
            return null;
        }

        return [
            'title' => $volumeInfo['title'] ?? '',
            'author' => implode(
                ', ',
                $volumeInfo['authors'] ?? []
            ),
            'isbn' => $this->findIsbn13(
                $volumeInfo['industryIdentifiers'] ?? [],
                $isbn
            ),
            'published_date' => $this->normalizeDate(
                $volumeInfo['publishedDate'] ?? null
            ),
            'description' => $volumeInfo['description'] ?? '',
            'image_url' => $this->normalizeImageUrl(
                $volumeInfo['imageLinks']['thumbnail'] ?? null
            ),
        ];
    }

    private function findIsbn13(
        array $identifiers,
        string $fallback
    ): string {
        foreach ($identifiers as $identifier) {
            if (($identifier['type'] ?? null) === 'ISBN_13') {
                return $identifier['identifier'];
            }
        }

        return $fallback;
    }

    private function normalizeDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        if (preg_match('/^\d{4}$/', $date)) {
            return "{$date}-01-01";
        }

        if (preg_match('/^\d{4}-\d{2}$/', $date)) {
            return "{$date}-01";
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        return null;
    }

    private function normalizeImageUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return str_replace('http://', 'https://', $url);
    }
}