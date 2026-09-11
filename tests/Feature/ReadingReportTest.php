<?php

namespace Tests\Feature;

use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Genre;

class ReadingReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_reading_report(): void
    {
        $response = $this->get('/reading-report');

        $response->assertRedirect('/login');
    }

    public function test_user_can_view_own_reading_report(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlan::STATUS_NOT_STARTED,
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlan::STATUS_READING,
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlan::STATUS_COMPLETED,
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
            'status' => ReadingPlan::STATUS_COMPLETED,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 4,
        ]);

        Review::factory()->create([
            'user_id' => $otherUser->id,
            'rating' => 1,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/reading-report');

        $response->assertOk();

        $response->assertViewHas('statusCounts', [
            'not_started' => 1,
            'reading' => 1,
            'completed' => 1,
        ]);

        $response->assertViewHas('reviewCount', 2);
        $response->assertViewHas('averageRating', 4.5);
    }

    public function test_favorite_genres_and_high_rated_books_are_aggregated(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $mystery = Genre::factory()->create([
            'name' => 'ミステリー',
        ]);

        $fantasy = Genre::factory()->create([
            'name' => 'ファンタジー',
        ]);

        $highRatedReview = Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        $middleRatedReview = Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 3,
        ]);

        $highRatedReview->book->genres()->attach([
            $mystery->id,
            $fantasy->id,
        ]);

        $middleRatedReview->book->genres()->attach($mystery->id);

        $otherReview = Review::factory()->create([
            'user_id' => $otherUser->id,
            'rating' => 1,
        ]);

        $otherReview->book->genres()->attach($fantasy->id);

        $response = $this
            ->actingAs($user)
            ->get('/reading-report');

        $response->assertOk();

        $response->assertViewHas(
            'favoriteGenres',
            fn ($genres) =>
                $genres->count() === 2
                && $genres->first()['genre']->is($mystery)
                && $genres->first()['count'] === 2
        );

        $response->assertViewHas(
            'highRatedReviews',
            fn ($reviews) =>
                $reviews->count() === 2
                && $reviews->first()->is($highRatedReview)
        );

        $response->assertSee('ミステリー');
        $response->assertSee($highRatedReview->book->title);
    }
}