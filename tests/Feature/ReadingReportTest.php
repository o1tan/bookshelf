<?php

namespace Tests\Feature;

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
}