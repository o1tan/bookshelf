<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_notifications(): void
    {
        $this
            ->get('/notifications')
            ->assertRedirect('/login');
    }

    public function test_user_can_view_own_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownBook = Book::factory()->create([
            'title' => '本人の通知対象書籍',
        ]);

        $otherBook = Book::factory()->create([
            'title' => '他人の通知対象書籍',
        ]);

        $ownPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $ownBook->id,
        ]);

        $otherPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
            'book_id' => $otherBook->id,
        ]);

        $user->notify(
            new ReadingPlanReminderNotification(
                $ownPlan->load('book')
            )
        );

        $otherUser->notify(
            new ReadingPlanReminderNotification(
                $otherPlan->load('book')
            )
        );

        $this
            ->actingAs($user)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('本人の通知対象書籍')
            ->assertDontSee('他人の通知対象書籍');
    }

    public function test_notification_page_displays_empty_message(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('新しい通知はありません。');
    }

        public function test_notifications_are_paginated(): void
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 11; $i++) {
            $book = Book::factory()->create([
                'title' => "通知対象書籍{$i}",
            ]);

            $readingPlan = ReadingPlan::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);

            $user->notify(
                new ReadingPlanReminderNotification(
                    $readingPlan->load('book')
                )
            );
        }

        $response = $this
            ->actingAs($user)
            ->get('/notifications?page=2');

        $response->assertOk();

        $notifications = $response->viewData('notifications');

        $this->assertSame(2, $notifications->currentPage());
        $this->assertSame(11, $notifications->total());
        $this->assertCount(1, $notifications->items());
    }
}