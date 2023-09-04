<?php

namespace App\Libraries\Services;

use App\Events\AchievementUnlocked;
use App\Libraries\Achievements\AchievementUnlockStrategy;
use App\Libraries\Achievements\CommentsWrittenStrategy;
use App\Libraries\Achievements\LessonsWatchedStrategy;
use App\Libraries\Enums\AchievementType;
use App\Models\Achievement;
use App\Models\User;

class AchievementService
{

    /**
     * @var array<AchievementUnlockStrategy> $strategies
     */
    protected array $strategies;

    public function __construct()
    {
        $this->strategies = [
            AchievementType::LESSONS_WATCHED->value => new LessonsWatchedStrategy(),
            AchievementType::COMMENTS_WRITTEN->value => new CommentsWrittenStrategy(),
        ];
    }

    public function unlockNextAchievement(User $user, AchievementType $achievementType): void
    {
        // get strategy for the achievement type
        $strategy = $this->strategies[$achievementType->value];

        $latestAchievement = $this->getLatestAchievement($user, $achievementType);

        // if the user does not have any achievement, unlock the first one
        if (!$latestAchievement) {
            $strategy->unlockIfNotUnlocked($user, $this->getFirstAchievement($achievementType));
            return;
        }

        $nextAchievement = $this->getNextAchievement($latestAchievement, $achievementType);
        if (!$nextAchievement) {
            return;
        }

        $strategy->unlockIfNotUnlocked($user, $nextAchievement);
    }

    public function getNextAchievement(Achievement $achievement, AchievementType $achievementType): ?Achievement
    {
        return Achievement::where('id', '>', $achievement->id)
            ->where('type', '=', $achievementType->value)
            ->first();
    }

    public function getFirstAchievement(AchievementType $achievementType): Achievement
    {
        return Achievement::where('type', '=', $achievementType->value)->first();
    }

    public function getLatestAchievement(User $user, AchievementType $achievementType): mixed
    {
        return $user->load(['achievements' => function($query) use ($achievementType) {
            $query->where('type', '=', $achievementType->value)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit(1);
        }])?->achievements?->last();
    }

}
