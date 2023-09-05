<?php

namespace App\Libraries\Achievements;

use App\Models\Achievement;
use App\Models\User;
use Psr\Log\LoggerInterface;

class LessonsWatchedStrategy extends AchievementUnlockStrategy
{

    protected LoggerInterface $logger;

    public function __construct()
    {
        parent::__construct();
        $this->logger = app(LoggerInterface::class);
    }

    public function checkCriteria(User $user, Achievement $achievement): bool
    {
        $this->logger->info('Checking criteria for achievement', ['user_id' => $user->id, 'achievement_id' => $achievement->id, 'achievementType' => $achievement->type]);

        $hasAchieved = $user->watched()->count() >= $achievement->target_count;

        $this->logger->log(
            $hasAchieved ? 'info' : 'notice',
            $hasAchieved ? 'User meets requirements for achievement' : 'User does not meet requirements for achievement',
            [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'achievementType' => $achievement->type,
                'watched_count' => $user->watched()->count(),
                'target_count' => $achievement->target_count,
            ]
        );

        return $hasAchieved;
    }
}
