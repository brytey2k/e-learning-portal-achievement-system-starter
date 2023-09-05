<?php

namespace App\Http\Controllers;

use App\Libraries\Services\AchievementService;
use App\Libraries\Services\BadgeService;
use App\Models\User;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{

    public function __construct(
        private readonly AchievementService $achievementService,
        private readonly BadgeService $badgeService,
        private readonly Cache $cache
    )
    {
    }

    public function index(User $user)
    {
        $cacheKey = "achievements.{$user->id}";
        $timeToLiveInMinutes = 60;

        // Retrieve the information from the Cache, or use the callback to calculate it
        $response = $this->cache->remember($cacheKey, $timeToLiveInMinutes, function () use ($user) {
            $user->load(['achievements', 'badge']);
            $nextAvailableAchievements = $this->achievementService->getNextAvailableAchievements($user);

            $nextBadge = $user->badge->getNextBadge();
            $remainingToUnlockNextBadge = $this->badgeService->getRemainingToUnlockNextBadge($nextBadge, $user);

            return [
                'unlocked_achievements' => $user->achievements->pluck('name')->toArray(),
                'next_available_achievements' => $nextAvailableAchievements,
                'current_badge' => $user->badge->name,
                'next_badge' => $nextBadge?->name,
                'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge,
            ];
        });

        return response()->json($response);
    }
}
