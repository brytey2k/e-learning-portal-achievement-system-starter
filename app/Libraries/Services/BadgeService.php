<?php

namespace App\Libraries\Services;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
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

        $nextBadge = $user->badge->getNextBadge();
        if(!$nextBadge) {
            return;
        }

        $this->unlockBadgeIfNotUnLocked($user, $nextBadge);
    }

    protected function unlockBadgeIfNotUnLocked(User $user, Badge $nextBadge): void
    {
        if($user->achievements->count() < $nextBadge->achievements_required) {
            return;
        }

        if($user->badge->id < $nextBadge->id) {
            $user->badge()->associate($nextBadge);
            $user->save();

            event(new BadgeUnlocked($nextBadge->name, $user));
        }
    }

}
