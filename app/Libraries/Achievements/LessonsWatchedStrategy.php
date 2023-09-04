<?php

namespace App\Libraries\Achievements;

use App\Models\Achievement;
use App\Models\User;

class LessonsWatchedStrategy extends AchievementUnlockStrategy
{
    public function checkCriteria(User $user, Achievement $achievement): bool
    {
        return $user->watched()->count() >= $achievement->target_count;
    }
}
