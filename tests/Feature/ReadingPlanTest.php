<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_reading_plans(): void
    {
        $response = $this->get('/reading-plans');

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'deadline' => now()->addDays(7)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_NOT_STARTED,
                'reminder_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlan::STATUS_NOT_STARTED,
        ]);
    }

    public function test_index_displays_only_users_reading_plans(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/reading-plans');

        $response->assertOk();
        $response->assertSee($ownPlan->book->title);
        $response->assertDontSee($otherPlan->book->title);
    }

    public function test_user_can_update_own_reading_plan(): void
    {
        $user = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlan::STATUS_NOT_STARTED,
        ]);

        $response = $this
            ->actingAs($user)
            ->put("/reading-plans/{$readingPlan->id}", [
                'deadline' => now()->addDays(10)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_READING,
                'reminder_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'status' => ReadingPlan::STATUS_READING,
        ]);
    }

    public function test_user_cannot_edit_or_update_another_users_plan(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this
            ->actingAs($user)
            ->get("/reading-plans/{$readingPlan->id}/edit")
            ->assertForbidden();

        $this
            ->actingAs($user)
            ->put("/reading-plans/{$readingPlan->id}", [
                'deadline' => now()->addDays(10)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_READING,
                'reminder_at' => null,
            ])
            ->assertForbidden();
    }

    public function test_user_can_delete_own_reading_plan(): void
    {
        $user = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete("/reading-plans/{$readingPlan->id}");

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseMissing('reading_plans', [
            'id' => $readingPlan->id,
        ]);
    }

    public function test_user_cannot_create_duplicate_plan_for_same_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'deadline' => now()->addDays(7)->format('Y-m-d'),
                'status' => ReadingPlan::STATUS_NOT_STARTED,
                'reminder_at' => null,
            ]);

        $response->assertSessionHasErrors('book_id');

        $this->assertDatabaseCount('reading_plans', 1);
    }

    public function test_reading_plan_rejects_invalid_input(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'deadline' => now()->subDay()->format('Y-m-d'),
                'status' => 'invalid-status',
                'reminder_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            ]);

        $response->assertSessionHasErrors([
            'deadline',
            'status',
            'reminder_at',
        ]);

        $this->assertDatabaseMissing('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_plan(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this
            ->actingAs($user)
            ->delete("/reading-plans/{$readingPlan->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
        ]);
    }
}
