<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Libraries\Services\BadgeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Psr\Log\LoggerInterface;

class AchievementUnlockedListener
{

    /**
     * Create the event listener.
     */
    public function __construct(private readonly BadgeService $badgeService, private readonly LoggerInterface $logger)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(AchievementUnlocked $event): void
    {
        $this->badgeService->unlockNextBadge($event->user);

        $this->logger->info('Achievement unlocked event handled', ['user' => $event->user->id]);
    }
}
