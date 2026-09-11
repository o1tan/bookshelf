<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IsbnLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_search_isbn(): void
    {
        $this->getJson('/books/isbn/9781234567897')
            ->assertUnauthorized();
    }

    public function test_isbn_must_be_thirteen_digits(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/books/isbn/123')
            ->assertStatus(422)
            ->assertJsonPath(
                'message',
                'ISBNは13桁の数字で入力してください。'
            );

        Http::assertNothingSent();
    }

    public function test_book_information_can_be_found_by_isbn(): void
    {
        Http::fake([
            'www.googleapis.com/*' => Http::response([
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'API検索テスト書籍',
                            'authors' => [
                                '山田太郎',
                                '佐藤花子',
                            ],
                            'publishedDate' => '2026-09',
                            'description' => '書籍説明',
                            'industryIdentifiers' => [
                                [
                                    'type' => 'ISBN_13',
                                    'identifier' => '9781234567897',
                                ],
                            ],
                            'imageLinks' => [
                                'thumbnail' => 'http://example.com/book.jpg',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/books/isbn/9781234567897');

        $response->assertOk()
            ->assertJsonPath(
                'data.title',
                'API検索テスト書籍'
            )
            ->assertJsonPath(
                'data.author',
                '山田太郎, 佐藤花子'
            )
            ->assertJsonPath(
                'data.isbn',
                '9781234567897'
            )
            ->assertJsonPath(
                'data.published_date',
                '2026-09-01'
            )
            ->assertJsonPath(
                'data.image_url',
                'https://example.com/book.jpg'
            );

        Http::assertSent(function ($request) {
            return $request['q'] ===
                'isbn:9781234567897';
        });
    }

    public function test_not_found_is_returned_when_book_does_not_exist(): void
    {
        Http::fake([
            'www.googleapis.com/*' => Http::response([
                'totalItems' => 0,
                'items' => [],
            ], 200),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/books/isbn/9781234567897')
            ->assertNotFound()
            ->assertJsonPath(
                'message',
                '該当する書籍が見つかりませんでした。'
            );
    }

    public function test_bad_gateway_is_returned_when_api_fails(): void
    {
        Http::fake([
            'www.googleapis.com/*' => Http::response([], 500),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/books/isbn/9781234567897')
            ->assertStatus(502)
            ->assertJsonPath(
                'message',
                '書籍情報の取得に失敗しました。'
            );
    }
}