<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Libraries\Services\BadgeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AchievementUnlockedListener
{

    protected BadgeService $badgeService;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->badgeService = new BadgeService();
    }

    /**
     * Handle the event.
     */
    public function handle(AchievementUnlocked $event): void
    {
        $this->badgeService->unlockNextBadge($event->user);
    }
}
