<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $user->load(['achievements', 'badge']);
        $nextAvailableAchievements = Achievement::whereDoesntHave('users', function($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        })->pluck('name')->toArray();

        $nextBadge = $user->badge->getNextBadge();

        $remainingToUnlockNextBadge = $nextBadge
            ? $nextBadge->achievements_required - $user->achievements->count() : 0;

        return response()->json([
            'unlocked_achievements' => $user->achievements->pluck('name')->toArray(),
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $user->badge->name,
            'next_badge' => $nextBadge?->name,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge,
        ]);
    }
}
