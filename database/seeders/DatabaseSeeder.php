<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Lesson;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AchievementSeeder::class);
        $this->call(BadgeSeeder::class);

        User::factory()
            ->count(10)
            ->create([
                'badge_id' => Badge::first()->id,
            ]);

        $lessons = Lesson::factory()
            ->count(20)
            ->create();

    }
}
