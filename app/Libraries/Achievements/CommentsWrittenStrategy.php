<?php

namespace App\Libraries\Achievements;

use App\Models\Achievement;
use App\Models\User;

class CommentsWrittenStrategy extends AchievementUnlockStrategy
{
    public function checkCriteria(User $user, Achievement $achievement): bool
    {
        return $user->comments()->count() >= $achievement->target_count;
    }
}
