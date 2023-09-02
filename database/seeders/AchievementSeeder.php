<?php

namespace Database\Seeders;

use App\Libraries\Enums\AchievementType;
use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            1 => 'First Lesson Watched',
            5 => '5 Lessons Watched',
            10 => '10 Lessons Watched',
            25 => '25 Lessons Watched',
            50 => '50 Lessons Watched',
        ])->each(fn ($achievement, $targetCount) => Achievement::factory()->create([
            'name' => $achievement,
            'type' => AchievementType::LESSONS_WATCHED,
            'target_count' => $targetCount,
        ]));

        collect([
            1 => 'First Comment Written',
            3 => '3 Comments Written',
            5 => '5 Comments Written',
            10 => '10 Comments Written',
            20 => '20 Comments Written',
        ])->each(fn ($achievement, $targetCount) => Achievement::factory()->create([
            'name' => $achievement,
            'type' => AchievementType::COMMENTS_WRITTEN,
            'target_count' => $targetCount,
        ]));
    }
}
