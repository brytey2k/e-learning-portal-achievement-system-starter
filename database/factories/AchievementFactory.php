<?php

namespace Database\Factories;

use App\Libraries\Enums\AchievementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement([AchievementType::LESSONS_WATCHED, AchievementType::COMMENTS_WRITTEN]),
            'target_count' => $this->faker->numberBetween(1, 10),
        ];
    }
}
