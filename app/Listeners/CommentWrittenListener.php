<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Libraries\Enums\AchievementType;
use App\Libraries\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentWrittenListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly AchievementService $achievementService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten $event): void
    {
        // unlock next achievement
        $this->achievementService->unlockNextAchievement($event->comment->user, AchievementType::COMMENTS_WRITTEN);
    }
}
