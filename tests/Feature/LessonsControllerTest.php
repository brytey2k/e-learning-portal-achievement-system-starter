<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Libraries\Enums\AchievementType;
use App\Listeners\AchievementUnlockedListener;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LessonsControllerTest extends TestCase
{

    use RefreshDatabase;

    public function testAnUnauthenticatedUserCannotWatchALesson(): void
    {
        $lesson = Lesson::factory()->create();

        $response = $this->get(route('lessons.view', ['lesson' => $lesson->id]));

        $response->assertRedirectToRoute('home');
    }

    public function testAnAuthenticatedUserCanWatchALesson(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create([
            'badge_id' => Badge::factory()->create()->id,
        ]);
        Achievement::factory()->create([
            'name' => 'Watched 3 Lessons',
            'target_count' => 3,
            'type' => AchievementType::LESSONS_WATCHED,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('lessons.view', ['lesson' => $lesson->id]));

        $response->assertJson([
            'message' => 'Lesson watched'
        ]);
        $this->assertDatabaseHas('lesson_user', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
            'watched' => true
        ]);
    }

    public function testWatchingEnoughLessonsUnlocksAchievement(): void
    {
        Event::fake(AchievementUnlocked::class);

        $user = User::factory()->create([
            'badge_id' => Badge::factory(['achievements_required' => 0])->create()->id,
        ]);
        $achievement = Achievement::factory()->create([
            'name' => 'Watched 3 Lessons',
            'target_count' => 3,
            'type' => AchievementType::LESSONS_WATCHED,
        ]);

        $this->actingAs($user);

        $lessons = Lesson::factory()->count($achievement->target_count)->create();

        foreach ($lessons as $lesson) {
            $this->get(route('lessons.view', ['lesson' => $lesson->id]));
        }

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
        ]);

        Event::assertDispatched(AchievementUnlocked::class);
        Event::assertListening(AchievementUnlocked::class, AchievementUnlockedListener::class);
    }

    public function testWatchingRequiredLessonsUnlocksBadge(): void
    {
        Event::fake(BadgeUnlocked::class);

        Achievement::factory()->create([
            'name' => 'Watched 3 Lessons',
            'target_count' => 0,
            'type' => AchievementType::LESSONS_WATCHED,
        ]);

        $firstBadge = Badge::factory()->create([
            'achievements_required' => 0,
        ]);
        $secondBadge = Badge::factory()->create([
            'achievements_required' => 1,
        ]);

        $user = User::factory()->create([
            'badge_id' => $firstBadge->id,
        ]);

        $this->actingAs($user);

        $lessons = Lesson::factory()->count(3)->create();
        foreach($lessons as $lesson) {
            $this->get(route('lessons.view', ['lesson' => $lesson->id]));
        }

        $this->assertEquals($secondBadge->id, $user->badge->id);

        Event::assertDispatched(BadgeUnlocked::class);
    }

}
