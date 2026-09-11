<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_uses_database_channel(): void
    {
        $user = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $notification = new ReadingPlanReminderNotification(
            $readingPlan
        );

        $this->assertSame(
            ['database'],
            $notification->via($user)
        );
    }

    public function test_notification_creates_expected_database_data(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'title' => '期限通知テスト書籍',
        ]);

        $deadline = now()->addDays(3)->startOfDay();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'deadline' => $deadline,
        ]);

        $notification = new ReadingPlanReminderNotification(
            $readingPlan->load('book')
        );

        $this->assertSame([
            'reading_plan_id' => $readingPlan->id,
            'book_id' => $book->id,
            'book_title' => '期限通知テスト書籍',
            'deadline' => $deadline->format('Y-m-d'),
            'message' => '「期限通知テスト書籍」の読了期限が近づいています。',
        ], $notification->toArray($user));
    }
}