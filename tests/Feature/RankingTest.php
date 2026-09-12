<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ranking_page_is_publicly_accessible(): void
    {
        $response = $this->get('/ranking');

        $response->assertStatus(200);
    }

    public function test_books_are_ranked_by_average_review_count_and_id(): void
    {
        $highestRatedBook = Book::factory()->create([
            'title' => '最高評価の書籍',
        ]);

        $firstTiedBook = Book::factory()->create([
            'title' => '同率でIDが小さい書籍',
        ]);

        $secondTiedBook = Book::factory()->create([
            'title' => '同率でIDが大きい書籍',
        ]);

        $fewerReviewsBook = Book::factory()->create([
            'title' => 'レビュー件数が少ない書籍',
        ]);

        $unreviewedBook = Book::factory()->create([
            'title' => 'レビューなしの書籍',
        ]);

        Review::factory()->for($highestRatedBook)->create([
            'rating' => 5,
        ]);

        Review::factory()->count(2)->for($firstTiedBook)->create([
            'rating' => 4,
        ]);

        Review::factory()->count(2)->for($secondTiedBook)->create([
            'rating' => 4,
        ]);

        Review::factory()->for($fewerReviewsBook)->create([
            'rating' => 4,
        ]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            '最高評価の書籍',
            '同率でIDが小さい書籍',
            '同率でIDが大きい書籍',
            'レビュー件数が少ない書籍',
        ]);

        $response->assertDontSee($unreviewedBook->title);
    }
}
