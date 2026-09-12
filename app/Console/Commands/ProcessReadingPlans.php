<?php

namespace App\Console\Commands;

use App\Models\ReadingPlan;
use Illuminate\Console\Command;

class ProcessReadingPlans extends Command
{
    protected $signature = 'reading-plans:process';

    protected $description =
        '期限切れの読書計画を失効し、通知対象の計画へ通知する';

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

        $this->info("期限切れ：{$expiredCount}件");

        return $this->call('reading-plans:send-reminders');
    }
}
