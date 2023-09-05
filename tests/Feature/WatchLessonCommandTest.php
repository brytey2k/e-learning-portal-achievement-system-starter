<?php

namespace Tests\Feature;

use App\Libraries\Enums\AchievementType;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WatchLessonCommandTest extends TestCase
{

    use RefreshDatabase;

    public function testWatchLessonCommandCanWatchALesson(): void
    {
        $user = User::factory()->create([
            'badge_id' => Badge::factory()->create(['achievements_required' => 0])->id
        ]);
        $lesson = Lesson::factory()->create();
        Achievement::factory()->create([
            'target_count' => 1,
            'type' => AchievementType::LESSONS_WATCHED->value,
        ]);

        $output = $this->artisan('app:watch-lesson', [
            '--user' => $user->id,
            '--lesson' => $lesson->id
        ]);

        $output->expectsOutput('Lesson watched');
        $output->assertExitCode(0);
    }

    public function testMissingUserWillGiveAnError(): void
    {
        $lesson = Lesson::factory()->create();

        $output = $this->artisan('app:watch-lesson', [
            '--user' => 9999953,
            '--lesson' => $lesson->id
        ]);

        $output->expectsOutput('User not found');
    }

    public function testMissingLessonWillGiveAnError(): void
    {
        $output = $this->artisan('app:watch-lesson', [
            '--lesson' => 9999953
        ]);

        $output->expectsOutput('Lesson not found');
    }
}
