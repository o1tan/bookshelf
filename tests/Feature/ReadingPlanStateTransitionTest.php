<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanStateTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_new_plan_after_previous_plan_expired(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlan::STATUS_EXPIRED,
        ]);

        $this
            ->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'deadline' => now()->addDays(7)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_NOT_STARTED,
                'reminder_at' => null,
            ])
            ->assertRedirect('/reading-plans');

        $this->assertDatabaseCount('reading_plans', 2);
    }

    public function test_updating_expired_plan_resets_status_and_reminder(): void
    {
        $user = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlan::STATUS_EXPIRED,
            'reminded_at' => now()->subDay(),
        ]);

        $this
            ->actingAs($user)
            ->put("/reading-plans/{$readingPlan->id}", [
                'deadline' => now()->addDays(7)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_READING,
                'reminder_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'status' => ReadingPlan::STATUS_NOT_STARTED,
            'reminded_at' => null,
        ]);
    }
}
