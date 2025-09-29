<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'ticker' => $this->faker->unique()->regexify('[A-Z]{2,5}'),
            'sector' => $this->faker->randomElement(['Technology', 'Healthcare', 'Finance', 'Energy', 'Consumer Goods']),
            'industry' => $this->faker->randomElement(['Software', 'Biotechnology', 'Banking', 'Oil & Gas', 'Retail']),
            'market_cap' => $this->faker->numberBetween(1000000, 1000000000000),
            'description' => $this->faker->paragraph(),
            'headquarters' => $this->faker->city() . ', ' . $this->faker->stateAbbr(),
            'employees' => $this->faker->numberBetween(10, 100000),
            'founded_date' => $this->faker->date(),
            'website_url' => $this->faker->url(),
            'reports_financial_data_in' => $this->faker->randomElement(['USD', 'EUR', 'CAD']),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
