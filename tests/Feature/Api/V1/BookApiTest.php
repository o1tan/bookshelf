<?php

namespace Tests\Feature\Api\V1;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_index_is_public_and_paginated(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books?per_page=2');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'title',
                        'author',
                        'isbn',
                        'published_date',
                        'description',
                        'image_url',
                        'genres',
                        'average_rating',
                        'reviews_count',
                        'created_at',
                        'updated_at',
                        'links' => [
                            'self',
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertSame(2, $response->json('meta.per_page'));
        $this->assertSame(3, $response->json('meta.total'));
    }

    public function test_book_detail_contains_user_and_reviews(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();
        $reviewUser = User::factory()->create();

        $book->genres()->attach($genre);

        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $reviewUser->id,
            'rating' => 5,
            'comment' => 'API詳細確認用レビュー',
        ]);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.user.id', $book->user_id)
            ->assertJsonPath('data.genres.0.id', $genre->id)
            ->assertJsonPath('data.average_rating', 5)
            ->assertJsonPath('data.reviews_count', 1)
            ->assertJsonPath('data.reviews.0.rating', 5)
            ->assertJsonPath(
                'data.reviews.0.comment',
                'API詳細確認用レビュー'
            )
            ->assertJsonPath(
                'data.reviews.0.user.id',
                $reviewUser->id
            );
    }

    public function test_book_can_be_created(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $genres = Genre::factory()->count(2)->create();

        $payload = [
            'title' => 'API登録テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '9781234567897',
            'published_date' => '2026-09-08',
            'description' => 'API登録テストです。',
            'image_url' => null,
            'genre_ids' => $genres->pluck('id')->all(),
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'API登録テスト書籍')
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonCount(2, 'data.genres');

        $bookId = $response->json('data.id');

        $this->assertDatabaseHas('books', [
            'id' => $bookId,
            'user_id' => $user->id,
            'isbn' => '9781234567897',
        ]);

        foreach ($genres as $genre) {
            $this->assertDatabaseHas('book_genre', [
                'book_id' => $bookId,
                'genre_id' => $genre->id,
            ]);
        }
    }

    public function test_book_can_be_updated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '9781234567897',
        ]);
        $oldGenre = Genre::factory()->create();
        $newGenre = Genre::factory()->create();

        $book->genres()->attach($oldGenre);

        $payload = [
            'title' => 'API更新済み書籍',
            'author' => '更新後の著者',
            'isbn' => '9781234567897',
            'published_date' => '2026-09-08',
            'description' => 'API更新テストです。',
            'image_url' => null,
            'genre_ids' => [$newGenre->id],
        ];

        $response = $this->putJson(
            "/api/v1/books/{$book->id}",
            $payload
        );

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'API更新済み書籍')
            ->assertJsonPath('data.genres.0.id', $newGenre->id);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'API更新済み書籍',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $newGenre->id,
        ]);

        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $oldGenre->id,
        ]);
    }

    public function test_book_can_be_deleted(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson(
            "/api/v1/books/{$book->id}"
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_book_store_returns_validation_errors(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/books', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'author',
                'genre_ids',
            ]);
    }

    public function test_guest_cannot_create_update_or_delete_book(): void
    {
        $book = Book::factory()->create();

        $this->postJson('/api/v1/books', [])
            ->assertUnauthorized();

        $this->putJson("/api/v1/books/{$book->id}", [])
            ->assertUnauthorized();

        $this->deleteJson("/api/v1/books/{$book->id}")
            ->assertUnauthorized();
    }

    public function test_user_cannot_update_or_delete_another_users_book(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($otherUser);

        $this->putJson("/api/v1/books/{$book->id}", [])
            ->assertForbidden();

        $this->deleteJson("/api/v1/books/{$book->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_book_can_be_created_with_bearer_token(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $token = $user->createToken('book-api-test')->plainTextToken;

        $payload = [
            'title' => 'Bearerトークン登録書籍',
            'author' => 'テスト著者',
            'isbn' => '9781234567890',
            'published_date' => '2026-09-10',
            'description' => 'Bearerトークン認証のテストです。',
            'image_url' => null,
            'genre_ids' => [$genre->id],
        ];

        $response = $this->withToken($token)
            ->postJson('/api/v1/books', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Bearerトークン登録書籍')
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('books', [
            'title' => 'Bearerトークン登録書籍',
            'user_id' => $user->id,
        ]);
    }

    public function test_book_index_can_search_by_keyword_and_filter_by_genre(): void
    {
        $genre = Genre::factory()->create();
        $otherGenre = Genre::factory()->create();

        $titleMatchedBook = Book::factory()->create([
            'title' => 'Laravel API入門',
            'author' => '山田太郎',
        ]);

        $authorMatchedBook = Book::factory()->create([
            'title' => 'PHP実践ガイド',
            'author' => 'Laravel研究会',
        ]);

        $outsideGenreBook = Book::factory()->create([
            'title' => 'Laravel設計入門',
            'author' => '鈴木花子',
        ]);

        $titleMatchedBook->genres()->attach($genre);
        $authorMatchedBook->genres()->attach($genre);
        $outsideGenreBook->genres()->attach($otherGenre);

        $response = $this->getJson(
            "/api/v1/books?keyword=Laravel&genre_id={$genre->id}"
        );

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        $bookIds = collect($response->json('data'))
            ->pluck('id')
            ->sort()
            ->values()
            ->all();

        $expectedIds = collect([
            $titleMatchedBook->id,
            $authorMatchedBook->id,
        ])->sort()->values()->all();

        $this->assertSame($expectedIds, $bookIds);
    }

    public function test_book_index_validates_search_parameters(): void
    {
        $response = $this->getJson('/api/v1/books?genre_id=999999');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('genre_id');

        $response = $this->getJson('/api/v1/books?per_page=101');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }
}
