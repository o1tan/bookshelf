<?php

namespace App\Console\Commands;

use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Console\Command;

class ProcessReadingPlans extends Command
{
    protected $signature = 'reading-plans:process';

    protected $description =
        '期限切れの読書計画を失効し、予定時刻を過ぎた通知を送信する';

    public function handle(): int
    {
        $expiredCount = ReadingPlan::query()
            ->whereDate('deadline', '<', today())
            ->whereNotIn('status', [
                ReadingPlan::STATUS_COMPLETED,
                ReadingPlan::STATUS_EXPIRED,
            ])
            ->update([
                'status' => ReadingPlan::STATUS_EXPIRED,
            ]);

        $reminderCount = 0;

        ReadingPlan::query()
            ->whereNotNull('reminder_at')
            ->whereNull('reminded_at')
            ->where('reminder_at', '<=', now())
            ->whereNotIn('status', [
                ReadingPlan::STATUS_COMPLETED,
                ReadingPlan::STATUS_EXPIRED,
            ])
            ->with(['user', 'book'])
            ->each(function (ReadingPlan $readingPlan) use (
                &$reminderCount
            ): void {
                $readingPlan->user->notify(
                    new ReadingPlanReminderNotification($readingPlan)
                );

                $readingPlan->update([
                    'reminded_at' => now(),
                ]);

                $reminderCount++;
            });

        $this->info(
            "期限切れ：{$expiredCount}件、通知：{$reminderCount}件"
        );

        return self::SUCCESS;
    }
}
