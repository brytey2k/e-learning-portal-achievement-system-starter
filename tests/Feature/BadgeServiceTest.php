<?php

namespace Tests\Feature;

use App\Events\BadgeUnlocked;
use App\Libraries\Services\BadgeService;
use App\Models\Badge;
use App\Models\User;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BadgeServiceTest extends TestCase
{

    use RefreshDatabase;

    public function testNewBadgeIsNotAssignedAfterLastBadgeIsReached(): void
    {
        Event::fake(BadgeUnlocked::class);
        $user = User::factory()->create([
            'badge_id' => Badge::factory()->create(['achievements_required' => 0])->id,
        ]);

        $this->assertEmpty(app(BadgeService::class)->unlockNextBadge($user));
        Event::assertNotDispatched(BadgeUnlocked::class);
    }
}
