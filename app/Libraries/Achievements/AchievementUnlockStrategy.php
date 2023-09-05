<?php

namespace App\Libraries\Achievements;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Cache\Repository as Cache;

abstract class AchievementUnlockStrategy
{
    private Cache $cache;

    public function __construct() {
        $this->cache = app(Cache::class);
    }
    abstract public function checkCriteria(User $user, Achievement $achievement): bool;

    public function unlockIfNotUnlocked(User $user, Achievement $achievement): void
    {
        $achievementExists = $user->achievements()
            ->where('achievement_id', $achievement->id)
            ->exists();

        if($this->checkCriteria($user, $achievement) && !$achievementExists)
        {
            $user->achievements()->syncWithoutDetaching($achievement->id);

            // invalidate the cache
            $this->cache->forget("achievements.{$user->id}");

            event(new AchievementUnlocked($achievement->name, $user));
        }
    }
}
