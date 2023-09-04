<?php

namespace App\Libraries\Services;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;

class BadgeService
{

    public function __construct()
    {
        //
    }

    public function unlockNextBadge(User $user): void
    {
        $user->load(['badge', 'achievements']);

        $nextBadge = $this->getNextUserBadge($user);
        if(!$nextBadge) {
            return;
        }

        $this->unlockBadgeIfNotUnlocked($user, $nextBadge);
    }

    protected function getNextUserBadge(User $user): ?Badge
    {
        return $user->badge->getNextBadge();
    }

    protected function hasRequiredAchievements(User $user, Badge $nextBadge): bool
    {
        return $user->achievements->count() >= $nextBadge->achievements_required;
    }

    protected function unlockBadgeIfNotUnlocked(User $user, Badge $nextBadge): void
    {
        if (!$this->hasRequiredAchievements($user, $nextBadge)) {
            return;
        }

        $this->unlockBadge($user, $nextBadge);
    }

    protected function unlockBadge(User $user, Badge $nextBadge): void
    {
        if($user->badge->id >= $nextBadge->id) {
            return;
        }

        $user->badge()->associate($nextBadge);
        $user->save();

        event(new BadgeUnlocked($nextBadge->name, $user));
    }

}
