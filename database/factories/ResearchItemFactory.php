<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResearchItem>
 */
class ResearchItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'summary' => $this->faker->paragraph(),
            'visibility' => $this->faker->randomElement(['public', 'team', 'private']),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'company_id' => \App\Models\Company::factory(),
            'category_id' => \App\Models\Category::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
