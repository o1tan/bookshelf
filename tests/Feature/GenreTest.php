<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_genre_management(): void
    {
        $response = $this->get('/genres');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_genres(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/genres');

        $response->assertStatus(200)
            ->assertSee($genre->name);
    }

    public function test_authenticated_user_can_manage_unused_genre(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/genres', [
                'name' => '登録ジャンル',
            ])
            ->assertRedirect('/genres');

        $genre = Genre::where('name', '登録ジャンル')->firstOrFail();

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '登録ジャンル',
        ]);

        $this->actingAs($user)
            ->put("/genres/{$genre->id}", [
                'name' => '更新ジャンル',
            ])
            ->assertRedirect('/genres');

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '更新ジャンル',
        ]);

        $this->actingAs($user)
            ->delete("/genres/{$genre->id}")
            ->assertRedirect('/genres');

        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_genre_used_by_book_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $book->genres()->attach($genre);

        $response = $this
            ->actingAs($user)
            ->delete("/genres/{$genre->id}");

        $response->assertRedirect('/genres')
            ->assertSessionHas(
                'error',
                '書籍に使用されているジャンルは削除できません。'
            );

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_genre_page_displays_only_related_books(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => '対象ジャンル',
        ]);
        $otherGenre = Genre::factory()->create([
            'name' => '別ジャンル',
        ]);
        $relatedBook = Book::factory()->create([
            'title' => '対象の書籍',
        ]);
        $unrelatedBook = Book::factory()->create([
            'title' => '対象外の書籍',
        ]);

        $relatedBook->genres()->attach($genre);
        $unrelatedBook->genres()->attach($otherGenre);

        $response = $this
            ->actingAs($user)
            ->get("/genres/{$genre->id}");

        $response->assertStatus(200)
            ->assertSee('対象ジャンルの書籍一覧')
            ->assertSee('対象の書籍')
            ->assertDontSee('対象外の書籍');
    }

    public function test_genre_page_displays_message_when_no_books_exist(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get("/genres/{$genre->id}");

        $response->assertStatus(200)
            ->assertSee('このジャンルに紐づく書籍はありません。');
    }
}
