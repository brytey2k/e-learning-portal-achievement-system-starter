<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            0 => 'Beginner',
            4 => 'Intermediate',
            5 => 'Advanced',
            10 => 'Master',
        ])->each(fn($badge, $achievementsRequired) => Badge::factory()->create([
            'name' => $badge,
            'achievements_required' => $achievementsRequired,
        ]));
    }
}
