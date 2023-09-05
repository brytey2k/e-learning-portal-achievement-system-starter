<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
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

class LessonTest extends TestCase
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
        $this->seed();

        $lesson = Lesson::factory()->create();
        $user = User::factory()->create([
            'badge_id' => Badge::first()->id,
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
        $this->seed();

        $user = User::factory()->create([
            'badge_id' => Badge::first()->id,
        ]);
        $achievement = Achievement::first();

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

}
