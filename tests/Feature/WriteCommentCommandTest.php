<?php

namespace Tests\Feature;

use App\Libraries\Enums\AchievementType;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WriteCommentCommandTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function testWriteCommentCommandCanWriteAComment(): void
    {
        $user = User::factory()->create([
            'badge_id' => Badge::factory()->create(['achievements_required' => 0])->id,
        ]);
        Achievement::factory()->create([
            'target_count' => 1,
            'type' => AchievementType::COMMENTS_WRITTEN->value,
        ]);

        $output = $this->artisan('app:write-comment', [
            '--user' => $user->id,
            '--comment' => $this->faker->text,
        ]);

        $output->expectsOutput('Comment written');
        $output->assertExitCode(0);
    }

    public function testMissingUserWillGiveAnError(): void
    {
        $output = $this->artisan('app:write-comment', [
            '--user' => 9999953
        ]);

        $output->expectsOutput('User not found');
    }

}
