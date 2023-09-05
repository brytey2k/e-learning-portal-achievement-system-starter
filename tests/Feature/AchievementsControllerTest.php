<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AchievementsControllerTest extends TestCase
{

    use RefreshDatabase;

    public function testUserAchievementsCanBeViewed(): void
    {
        $user = User::factory()->create([
            'badge_id' => Badge::factory()->create(['achievements_required' => 0])->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('users.achievements.index', ['user' => $user->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unlocked_achievements',
            'next_available_achievements',
            'current_badge',
            'next_badge',
            'remaining_to_unlock_next_badge',
        ]);
    }

}
