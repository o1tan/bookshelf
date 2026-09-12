<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanReminderScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_plan_with_arbitrary_reminder_time(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'deadline' => now()->addDays(2)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_NOT_STARTED,
                'reminder_at' => now()
                    ->addDay()
                    ->setTime(10, 0)
                    ->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_reading_plan_rejects_reminder_after_deadline(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'deadline' => now()->addDays(2)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_NOT_STARTED,
                'reminder_at' => now()
                    ->addDays(3)
                    ->setTime(10, 0)
                    ->format('Y-m-d H:i:s'),
            ]);

        $response->assertSessionHasErrors('reminder_at');

        $this->assertDatabaseMissing('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
