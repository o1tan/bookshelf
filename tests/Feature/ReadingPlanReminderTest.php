<?php

namespace Tests\Feature;

use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReadingPlanReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_expires_overdue_reading_plans(): void
    {
        $expiredPlan = ReadingPlan::factory()->create([
            'deadline' => today()->subDay(),
            'status' => ReadingPlan::STATUS_READING,
        ]);

        $completedPlan = ReadingPlan::factory()->create([
            'deadline' => today()->subDay(),
            'status' => ReadingPlan::STATUS_COMPLETED,
        ]);

        $this->artisan('reading-plans:process')
            ->assertSuccessful();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $expiredPlan->id,
            'status' => ReadingPlan::STATUS_EXPIRED,
        ]);

        $this->assertDatabaseHas('reading_plans', [
            'id' => $completedPlan->id,
            'status' => ReadingPlan::STATUS_COMPLETED,
        ]);
    }

    public function test_command_sends_due_reminder_only_once(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'deadline' => today()->addDays(3),
            'status' => ReadingPlan::STATUS_READING,
            'reminder_at' => now()->subMinute(),
            'reminded_at' => null,
        ]);

        $this->artisan('reading-plans:process')
            ->assertSuccessful();

        Notification::assertSentTo(
            $user,
            ReadingPlanReminderNotification::class
        );

        $this->assertNotNull(
            $readingPlan->fresh()->reminded_at
        );

        $this->artisan('reading-plans:process')
            ->assertSuccessful();

        Notification::assertSentToTimes(
            $user,
            ReadingPlanReminderNotification::class,
            1
        );
    }

    public function test_command_does_not_send_future_reminder(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'deadline' => today()->addDays(3),
            'status' => ReadingPlan::STATUS_READING,
            'reminder_at' => now()->addDay(),
            'reminded_at' => null,
        ]);

        $this->artisan('reading-plans:process')
            ->assertSuccessful();

        Notification::assertNothingSent();
    }
}