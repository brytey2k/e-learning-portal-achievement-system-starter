<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Libraries\Enums\AchievementType;
use App\Libraries\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LessonWatchedListener
{

    protected AchievementService $achievementService;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->achievementService = new AchievementService();
    }

    /**
     * Handle the event.
     */
    public function handle(LessonWatched $event): void
    {
        $this->achievementService->unlockNextAchievement($event->user, AchievementType::LESSONS_WATCHED);
    }
}
