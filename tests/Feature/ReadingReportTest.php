<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_reading_report(): void
    {
        $this->get('/reports')
            ->assertRedirect('/login');
    }

    public function test_user_can_view_own_reading_report(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlan::STATUS_COMPLETED,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 4,
        ]);

        $response = $this->actingAs($user)->get('/reports');

        $response->assertOk()
            ->assertViewHas('reviewCount', 1)
            ->assertViewHas('completedBookCount', 1)
            ->assertViewHas('averageRating', 4.0)
            ->assertViewHas(
                'ratingDistribution',
                fn ($distribution) => $distribution->get(4) === 1
            );
    }

    public function test_report_aggregates_high_rated_books_and_genre_trends(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => '技術書',
        ]);

        $highRatedBook = Book::factory()->create();
        $lowRatedBook = Book::factory()->create();

        $highRatedBook->genres()->attach($genre);
        $lowRatedBook->genres()->attach($genre);

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $highRatedBook->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $lowRatedBook->id,
            'rating' => 3,
        ]);

        $response = $this->actingAs($user)->get('/reports');

        $response->assertOk()
            ->assertViewHas(
                'highRatedReviews',
                fn ($reviews) => $reviews->count() === 1
                    && $reviews->first()->is($highRatedBook->reviews->first())
            )
            ->assertViewHas(
                'genreRatingTrends',
                fn ($trends) => $trends->count() === 1
                    && $trends->first()['genre']->is($genre)
                    && (float) $trends->first()['average_rating'] === 4.0
                    && $trends->first()['count'] === 2
            );
    }
}
