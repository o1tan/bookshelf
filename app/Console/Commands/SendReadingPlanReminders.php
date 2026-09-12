<?php

namespace App\Console\Commands;

use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendReadingPlanReminders extends Command
{
    protected $signature = 'reading-plans:send-reminders';

    protected $description = '通知予定日時に到達した読書計画へ通知する';

    public function handle(): int
    {
        $reminderCount = 0;

        ReadingPlan::query()
            ->whereNotNull('reminder_at')
            ->whereNull('reminded_at')
            ->where('reminder_at', '<=', now())
            ->whereDate('deadline', '>=', today())
            ->whereIn('status', [
                ReadingPlan::STATUS_NOT_STARTED,
                ReadingPlan::STATUS_READING,
            ])
            ->select('id')
            ->chunkById(100, function ($plans) use (&$reminderCount): void {
                foreach ($plans as $plan) {
                    $sent = DB::transaction(function () use ($plan): bool {
                        $readingPlan = ReadingPlan::query()
                            ->lockForUpdate()
                            ->find($plan->id);

                        if ($readingPlan === null) {
                            return false;
                        }

                        if (
                            $readingPlan->reminder_at === null
                            || $readingPlan->reminded_at !== null
                            || $readingPlan->reminder_at->isFuture()
                            || $readingPlan->deadline->lt(today())
                            || ! in_array($readingPlan->status, [
                                ReadingPlan::STATUS_NOT_STARTED,
                                ReadingPlan::STATUS_READING,
                            ], true)
                        ) {
                            return false;
                        }

                        $readingPlan->load(['user', 'book']);

                        $readingPlan->user->notify(
                            new ReadingPlanReminderNotification($readingPlan)
                        );

                        $readingPlan->update([
                            'reminded_at' => now(),
                        ]);

                        return true;
                    });

                    if ($sent) {
                        $reminderCount++;
                    }
                }
            });

        $this->info("通知：{$reminderCount}件");

        return self::SUCCESS;
    }
}
