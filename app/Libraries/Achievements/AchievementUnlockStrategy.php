<?php

namespace App\Libraries\Achievements;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;

abstract class AchievementUnlockStrategy
{
    abstract public function checkCriteria(User $user, Achievement $achievement): bool;

    public function unlockIfNotUnlocked(User $user, Achievement $achievement): void
    {
        if($this->checkCriteria($user, $achievement)
            && !$user->achievements()
                ->where('achievement_id', $achievement->id)
                ->exists())
        {
            $user->achievements()->syncWithoutDetaching($achievement->id);
            event(new AchievementUnlocked($achievement->name, $user));
        }
    }
}
