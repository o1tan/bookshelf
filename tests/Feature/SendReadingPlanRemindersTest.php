<?php

namespace Tests\Feature;

use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendReadingPlanRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder_is_sent_once_when_scheduled_time_arrives(): void
    {
        Notification::fake();

        $this->travelTo(
            now()->setDate(2026, 9, 15)->setTime(15, 29, 0)
        );

        $user = User::factory()->create();

        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'deadline' => today(),
            'status' => ReadingPlan::STATUS_READING,
            'reminder_at' => today()->setTime(15, 30, 0),
            'reminded_at' => null,
        ]);

        $this->artisan('reading-plans:send-reminders')
            ->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertNull($plan->fresh()->reminded_at);

        $this->travel(1)->minutes();

        $this->artisan('reading-plans:send-reminders')
            ->assertSuccessful();

        Notification::assertSentToTimes(
            $user,
            ReadingPlanReminderNotification::class,
            1
        );

        $this->assertTrue(
            $plan->fresh()->reminded_at->equalTo(now())
        );

        $this->artisan('reading-plans:send-reminders')
            ->assertSuccessful();

        Notification::assertSentToTimes(
            $user,
            ReadingPlanReminderNotification::class,
            1
        );
    }

    public function test_overdue_plan_is_not_notified_before_daily_expiry_update(): void
    {
        Notification::fake();

        $this->travelTo(
            now()->setDate(2026, 9, 15)->setTime(0, 1, 0)
        );

        $plan = ReadingPlan::factory()->create([
            'deadline' => today()->subDay(),
            'status' => ReadingPlan::STATUS_READING,
            'reminder_at' => today()->subDay()->setTime(15, 30, 0),
            'reminded_at' => null,
        ]);

        $this->artisan('reading-plans:send-reminders')
            ->assertSuccessful();

        Notification::assertNothingSent();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'status' => ReadingPlan::STATUS_READING,
            'reminded_at' => null,
        ]);
    }

    public function test_completed_plan_is_not_notified(): void
    {
        Notification::fake();

        $this->travelTo(
            now()->setDate(2026, 9, 15)->setTime(15, 30, 0)
        );

        $plan = ReadingPlan::factory()->create([
            'deadline' => today()->addDay(),
            'status' => ReadingPlan::STATUS_COMPLETED,
            'completed_at' => now()->subHour(),
            'reminder_at' => now(),
            'reminded_at' => null,
        ]);

        $this->artisan('reading-plans:send-reminders')
            ->assertSuccessful();

        Notification::assertNothingSent();
        $this->assertNull($plan->fresh()->reminded_at);
    }

    public function test_daily_batch_does_not_resend_notification(): void
    {
        Notification::fake();

        $this->travelTo(
            now()->setDate(2026, 9, 15)->setTime(9, 0, 0)
        );

        $user = User::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'deadline' => today()->addDay(),
            'status' => ReadingPlan::STATUS_NOT_STARTED,
            'reminder_at' => now(),
            'reminded_at' => null,
        ]);

        $this->artisan('reading-plans:send-reminders')
            ->assertSuccessful();

        $this->artisan('reading-plans:process')
            ->assertSuccessful();

        Notification::assertSentToTimes(
            $user,
            ReadingPlanReminderNotification::class,
            1
        );
    }
}
