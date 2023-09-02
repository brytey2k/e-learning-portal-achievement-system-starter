<?php

namespace App\Libraries\Services;

use App\Libraries\Enums\AchievementType;
use App\Models\Achievement;
use App\Models\User;

class AchievementService
{

    public function __construct()
    {
        //
    }

    public function unlockNextAchievement(User $user, AchievementType $achievementType): void
    {
        $latestAchievement = $this->getLatestAchievement($user, $achievementType);
        dump($latestAchievement?->name);

        if(!$latestAchievement) {
            $user->achievements()->syncWithoutDetaching($this->getFirstAchievement($achievementType)->id);
            return;
        }

        $nextAchievement = $this->getNextAchievement($latestAchievement, $achievementType);
        if(!$nextAchievement) {
            return;
        }

        $this->unlockAchievementIfNotUnlocked($user, $nextAchievement);

//        if($achievementType === AchievementType::COMMENTS_WRITTEN) {
//            $this->unlockAchievementIfNotUnlocked($user, $nextAchievement);
//        } elseif($achievementType === AchievementType::LESSONS_WATCHED) {
//
//        }
    }

    public function getNextAchievement(Achievement $achievement, AchievementType $achievementType): ?Achievement
    {
        return Achievement::where('id', '>', $achievement->id)
            ->where('type', '=', $achievementType->value)
            ->first();
    }

    public function unlockAchievementIfNotUnlocked(User $user, Achievement $achievement): void
    {
        if($achievement->type === AchievementType::LESSONS_WATCHED->value) {
            if($user->watched()->count() < $achievement->target_count) {
                return;
            }
        } elseif($achievement->type === AchievementType::COMMENTS_WRITTEN->value) {
            if($user->comments()->count() < $achievement->target_count) {
                return;
            }
        }

        if (!$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
            $this->unlockAchievement($user, $achievement);
        }
    }

    public function unlockAchievement(User $user, Achievement $achievement): void
    {
        $user->achievements()->syncWithoutDetaching($achievement->id);
    }

    public function getFirstAchievement(AchievementType $achievementType): Achievement
    {
        return Achievement::where('type', '=', $achievementType->value)->first();
    }

    /**
     * @param User $user
     * @return Achievement|mixed|null
     */
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
