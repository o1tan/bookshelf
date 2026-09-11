<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_index_and_detail_are_publicly_accessible(): void
    {
        $book = Book::factory()->create([
            'title' => '公開確認用書籍',
        ]);

        $this->get(route('books.index'))
            ->assertOk()
            ->assertSee('公開確認用書籍');

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertSee('公開確認用書籍');
    }

    public function test_guest_cannot_access_book_creation_page(): void
    {
        $this->get(route('books.create'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_book(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('books.store'), [
                'title' => 'Laravel入門',
                'author' => 'テスト著者',
                'isbn' => '9781234567897',
                'published_date' => '2026-09-09',
                'description' => '書籍登録テストです。',
                'image_url' => null,
                'genres' => [$genre->id],
            ]);

        $book = Book::where('isbn', '9781234567897')->firstOrFail();

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'title' => 'Laravel入門',
            'isbn' => '9781234567897',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_owner_can_update_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $genre = Genre::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put(route('books.update', $book), [
                'title' => '更新後の書籍',
                'author' => '更新後の著者',
                'isbn' => $book->isbn,
                'published_date' => '2026-09-09',
                'description' => '更新しました。',
                'image_url' => null,
                'genres' => [$genre->id],
            ]);

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後の書籍',
            'author' => '更新後の著者',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_user_cannot_update_another_users_book(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->for($owner)->create();
        $genre = Genre::factory()->create();

        $this->actingAs($otherUser)
            ->put(route('books.update', $book), [
                'title' => '不正な更新',
                'author' => $book->author,
                'isbn' => $book->isbn,
                'published_date' => $book->published_date,
                'description' => $book->description,
                'image_url' => $book->image_url,
                'genres' => [$genre->id],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
            'title' => '不正な更新',
        ]);
    }

    public function test_owner_can_delete_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('books.destroy', $book))
            ->assertRedirect(route('books.index'));

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_book(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->for($owner)->create();

        $this->actingAs($otherUser)
            ->delete(route('books.destroy', $book))
            ->assertForbidden();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
        ]);
    }

    public function test_books_can_be_searched_by_title_or_author(): void
    {
        Book::factory()->create([
            'title' => 'Laravel入門',
            'author' => '山田太郎',
        ]);

        Book::factory()->create([
            'title' => 'PHP実践',
            'author' => 'Laravel研究会',
        ]);

        Book::factory()->create([
            'title' => 'JavaScript入門',
            'author' => '佐藤花子',
        ]);

        $response = $this->get('/books?keyword=Laravel');

        $response->assertOk()
            ->assertSee('Laravel入門')
            ->assertSee('PHP実践')
            ->assertDontSee('JavaScript入門');
    }

    public function test_books_can_be_filtered_by_genre(): void
    {
        $targetGenre = Genre::factory()->create([
            'name' => '技術書',
        ]);

        $otherGenre = Genre::factory()->create([
            'name' => '小説',
        ]);

        $targetBook = Book::factory()->create([
            'title' => '表示される技術書',
        ]);

        $otherBook = Book::factory()->create([
            'title' => '表示されない小説',
        ]);

        $targetBook->genres()->attach($targetGenre);
        $otherBook->genres()->attach($otherGenre);

        $response = $this->get(
            "/books?genre={$targetGenre->id}"
        );

        $response->assertOk()
            ->assertSee('表示される技術書')
            ->assertDontSee('表示されない小説');
    }

    public function test_books_can_be_sorted_by_oldest_and_title(): void
    {
        $olderBook = Book::factory()->create([
            'title' => 'Zの本',
            'created_at' => now()->subDay(),
        ]);

        $newerBook = Book::factory()->create([
            'title' => 'Aの本',
            'created_at' => now(),
        ]);

        $oldestResponse = $this->get('/books?sort=oldest');

        $oldestIds = $oldestResponse
            ->viewData('books')
            ->getCollection()
            ->pluck('id')
            ->all();

        $this->assertSame([
            $olderBook->id,
            $newerBook->id,
        ], $oldestIds);

        $titleResponse = $this->get('/books?sort=title');

        $titleIds = $titleResponse
            ->viewData('books')
            ->getCollection()
            ->pluck('id')
            ->all();

        $this->assertSame([
            $newerBook->id,
            $olderBook->id,
        ], $titleIds);
    }

    public function test_books_can_be_sorted_by_rating_with_unrated_last(): void
    {
        $highBook = Book::factory()->create();
        $lowBook = Book::factory()->create();
        $unratedBook = Book::factory()->create();

        Review::factory()->create([
            'book_id' => $highBook->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'book_id' => $lowBook->id,
            'rating' => 3,
        ]);

        $response = $this->get('/books?sort=rating');

        $bookIds = $response
            ->viewData('books')
            ->getCollection()
            ->pluck('id')
            ->all();

        $this->assertSame([
            $highBook->id,
            $lowBook->id,
            $unratedBook->id,
        ], $bookIds);
    }

    public function test_pagination_keeps_search_conditions(): void
    {
        Book::factory()->count(11)->create([
            'title' => 'Laravel Book',
        ]);

        $response = $this->get(
            '/books?keyword=Laravel&sort=title'
        );

        $nextPageUrl = $response
            ->viewData('books')
            ->nextPageUrl();

        $this->assertStringContainsString(
            'keyword=Laravel',
            $nextPageUrl
        );

        $this->assertStringContainsString(
            'sort=title',
            $nextPageUrl
        );
    }

    public function test_books_can_be_exported_to_csv_with_filters(): void
    {
        Book::factory()->create([
            'title' => 'CSV対象書籍',
            'author' => '対象著者',
        ]);

        Book::factory()->create([
            'title' => '出力しない書籍',
            'author' => '別の著者',
        ]);

        $response = $this->get(
            '/books/export?keyword=CSV対象'
        );

        $response->assertOk()
            ->assertDownload();

        $csv = $response->streamedContent();

        $this->assertStringContainsString(
            'CSV対象書籍',
            $csv
        );

        $this->assertStringNotContainsString(
            '出力しない書籍',
            $csv
        );
    }
}
