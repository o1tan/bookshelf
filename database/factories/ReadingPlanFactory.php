<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadingPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'deadline' => fake()
                ->dateTimeBetween('now', '+1 year')
                ->format('Y-m-d'),
            'status' => fake()->randomElement([
                ReadingPlan::STATUS_NOT_STARTED,
                ReadingPlan::STATUS_READING,
                ReadingPlan::STATUS_COMPLETED,
            ]),
            'reminder_at' => fake()->optional()->dateTimeBetween(
                'now',
                '+6 months'
            ),
        ];
    }
}