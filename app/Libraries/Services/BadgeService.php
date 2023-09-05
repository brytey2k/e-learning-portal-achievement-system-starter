<?php

namespace App\Libraries\Services;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Cache\Repository as Cache;
use Psr\Log\LoggerInterface;

class BadgeService
{

    public function __construct(private readonly LoggerInterface $logger, private readonly Cache $cache)
    {
        //
    }

    public function unlockNextBadge(User $user): void
    {
        $this->logger->info('Unlock next badge process started.', ['user_id' => $user->id]);

        $user->load(['badge', 'achievements']);

        $nextBadge = $this->getNextUserBadge($user);
        if(!$nextBadge) {
            $this->logger->info('No next badge found.', ['user_id' => $user->id]);
            return;
        }

        $this->unlockBadgeIfNotUnlocked($user, $nextBadge);
    }

    public function getRemainingToUnlockNextBadge(?Badge $nextBadge, User $user): int
    {
        $remaining = $nextBadge ?
            $nextBadge->achievements_required - $user->achievements->count() : 0;

        $this->logger->info('Calculated remaining achievements to unlock next badge.',
            ['user_id' => $user->id, 'remaining' => $remaining]);

        return $remaining;
    }

    protected function getNextUserBadge(User $user): ?Badge
    {
        $this->logger->info('Getting next user badge.', ['user_id' => $user->id]);

        return $user->badge->getNextBadge();
    }

    protected function hasRequiredAchievements(User $user, Badge $nextBadge): bool
    {
        $hasRequiredAchievements = $user->achievements->count() >= $nextBadge->achievements_required;

        $this->logger->info('Checked if user has required achievements.',
            ['user_id' => $user->id, 'has_required_achievements' => $hasRequiredAchievements]);

        return $hasRequiredAchievements;
    }

    protected function unlockBadgeIfNotUnlocked(User $user, Badge $nextBadge): void
    {
        if (!$this->hasRequiredAchievements($user, $nextBadge)) {
            $this->logger->info('User does not have required achievements.', ['user_id' => $user->id]);
            return;
        }

        $this->unlockBadge($user, $nextBadge);
    }

    protected function unlockBadge(User $user, Badge $nextBadge): void
    {
        if($user->badge->id >= $nextBadge->id) {
            $this->logger->info('Badge is already unlocked or higher.',
                ['user_id' => $user->id, 'badge_id' => $user->badge->id]);
            return;
        }

        $user->badge()->associate($nextBadge);
        $user->save();

        // invalidate cache
        $this->cache->forget("achievements.{$user->id}");

        $this->logger->info('Unlocked new badge for user.',
            ['user_id' => $user->id, 'new_badge_id' => $nextBadge->id]);

        event(new BadgeUnlocked($nextBadge->name, $user));
    }

}
