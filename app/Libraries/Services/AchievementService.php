<?php

namespace App\Libraries\Services;

use App\Libraries\Achievements\AchievementUnlockStrategy;
use App\Libraries\Achievements\CommentsWrittenStrategy;
use App\Libraries\Achievements\LessonsWatchedStrategy;
use App\Libraries\Enums\AchievementType;
use App\Models\Achievement;
use App\Models\User;
use Psr\Log\LoggerInterface;

class AchievementService
{

    /**
     * @var array<AchievementUnlockStrategy> $strategies
     */
    protected array $strategies;

    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->strategies = [
            AchievementType::LESSONS_WATCHED->value => new LessonsWatchedStrategy(),
            AchievementType::COMMENTS_WRITTEN->value => new CommentsWrittenStrategy(),
        ];
    }

    public function unlockNextAchievement(User $user, AchievementType $achievementType): void
    {
        $this->logger->info('Getting latest achievement for user', ['user_id' => $user->id, 'achievementType' => $achievementType->value]);
        // get strategy for the achievement type
        $strategy = $this->strategies[$achievementType->value];

        $latestAchievement = $this->getLatestAchievement($user, $achievementType);

        // if the user does not have any achievement, unlock the first one
        if (!$latestAchievement) {
            $strategy->unlockIfNotUnlocked($user, $this->getFirstAchievement($achievementType));

            $this->logger->info('User does not have any achievement. Unlocking the first one', ['user_id' => $user->id, 'achievementType' => $achievementType->value]);

            return;
        }

        $nextAchievement = $this->getNextAchievement($latestAchievement, $achievementType);
        if (!$nextAchievement) {
            info('No next achievement found', ['user_id' => $user->id, 'achievementType' => $achievementType->value]);

            return;
        }

        $strategy->unlockIfNotUnlocked($user, $nextAchievement);

        $this->logger->info('Unlocking next achievement', ['user_id' => $user->id, 'achievementType' => $achievementType->value]);
    }

    public function getNextAchievement(Achievement $achievement, AchievementType $achievementType): ?Achievement
    {
        $this->logger->info('Getting next achievement', ['achievement_id' => $achievement->id, 'achievementType' => $achievementType->value]);
        return Achievement::where('id', '>', $achievement->id)
            ->where('type', '=', $achievementType->value)
            ->first();
    }

    public function getNextAvailableAchievements(User $user): array
    {
        $this->logger->info('Getting next available achievements', ['user_id' => $user->id]);

        return Achievement::whereDoesntHave('users', function($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        })->pluck('name')->toArray();
    }

    public function getFirstAchievement(AchievementType $achievementType): Achievement
    {
        $this->logger->info('Getting first achievement', ['achievementType' => $achievementType->value]);

        return Achievement::where('type', '=', $achievementType->value)->first();
    }

    public function getLatestAchievement(User $user, AchievementType $achievementType): mixed
    {
        $this->logger->info('Getting latest achievement', ['user_id' => $user->id, 'achievementType' => $achievementType->value]);

        return $user->load(['achievements' => function($query) use ($achievementType) {
            $query->where('type', '=', $achievementType->value)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit(1);
        }])?->achievements?->last();
    }

}
