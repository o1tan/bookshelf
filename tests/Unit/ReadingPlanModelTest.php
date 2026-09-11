<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class ReadingPlanModelTest extends TestCase
{
    public function test_reading_plan_belongs_to_user(): void
    {
        $readingPlan = new ReadingPlan();

        $relation = $readingPlan->user();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(User::class, $relation->getRelated());
    }

    public function test_reading_plan_belongs_to_book(): void
    {
        $readingPlan = new ReadingPlan();

        $relation = $readingPlan->book();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertInstanceOf(Book::class, $relation->getRelated());
    }

    public function test_reading_plan_casts_dates(): void
    {
        $readingPlan = new ReadingPlan([
            'deadline' => '2026-09-30',
            'reminder_at' => '2026-09-29 09:00:00',
            'reminded_at' => '2026-09-29 09:05:00',
        ]);

        $this->assertInstanceOf(
            CarbonInterface::class,
            $readingPlan->deadline
        );

        $this->assertInstanceOf(
            CarbonInterface::class,
            $readingPlan->reminder_at
        );

        $this->assertInstanceOf(
            CarbonInterface::class,
            $readingPlan->reminded_at
        );
    }

    public function test_reading_plan_has_expected_statuses(): void
    {
        $this->assertSame(
            'not_started',
            ReadingPlan::STATUS_NOT_STARTED
        );

        $this->assertSame(
            'reading',
            ReadingPlan::STATUS_READING
        );

        $this->assertSame(
            'completed',
            ReadingPlan::STATUS_COMPLETED
        );

        $this->assertSame(
            'expired',
            ReadingPlan::STATUS_EXPIRED
        );
    }
}