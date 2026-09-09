<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteAndReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_favorite_or_review_like(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create();

        $this->post(route('favorites.store', $book))
            ->assertRedirect(route('login'));

        $this->post(route('review-likes.store', $review))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('favorites', 0);
        $this->assertDatabaseCount('review_likes', 0);
    }

    public function test_authenticated_user_can_add_book_to_favorites(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user)
            ->post(route('favorites.store', $book))
            ->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_favorite_is_not_created_twice(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user)
            ->post(route('favorites.store', $book));

        $this->actingAs($user)
            ->post(route('favorites.store', $book));

        $this->assertDatabaseCount('favorites', 1);
    }

    public function test_favorite_index_displays_only_users_favorites(): void
    {
        $user = User::factory()->create();

        $favoriteBook = Book::factory()->create([
            'title' => 'お気に入りの書籍',
        ]);

        $otherBook = Book::factory()->create([
            'title' => 'お気に入りではない書籍',
        ]);

        $user->favoriteBooks()->attach($favoriteBook->id);

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('お気に入りの書籍')
            ->assertDontSee('お気に入りではない書籍');
    }

    public function test_authenticated_user_can_remove_favorite(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book->id);

        $this->actingAs($user)
            ->delete(route('favorites.destroy', $book))
            ->assertRedirect(route('books.show', $book));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_authenticated_user_can_like_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $this->actingAs($user)
            ->post(route('review-likes.store', $review))
            ->assertRedirect(
                route('books.show', $review->book_id)
            );

        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_review_like_is_not_created_twice(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $this->actingAs($user)
            ->post(route('review-likes.store', $review));

        $this->actingAs($user)
            ->post(route('review-likes.store', $review));

        $this->assertDatabaseCount('review_likes', 1);
    }

    public function test_user_cannot_like_own_review(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('review-likes.store', $review))
            ->assertForbidden();

        $this->assertDatabaseCount('review_likes', 0);
    }

    public function test_authenticated_user_can_remove_review_like(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $user->likedReviews()->attach($review->id);

        $this->actingAs($user)
            ->delete(route('review-likes.destroy', $review))
            ->assertRedirect(
                route('books.show', $review->book_id)
            );

        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }
}