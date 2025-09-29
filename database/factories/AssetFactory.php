<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'file_name' => $this->faker->word() . '.pdf',
            'file_path' => 'research-assets/' . $this->faker->uuid() . '.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(1024, 10240),
            'source_type' => 'document',
            'source_id' => 0,
            'media_id' => null,
            'company_id' => \App\Models\Company::factory(),
            'user_id' => \App\Models\User::factory(),
            'visibility' => $this->faker->randomElement(['public', 'team', 'private']),
            'is_orphaned' => false,
            'created_via' => 'document',
        ];
    }
}
