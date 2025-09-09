<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ResearchItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create categories
        $categories = [
            ['name' => 'Financial Analysis', 'slug' => 'financial-analysis', 'color' => '#3B82F6'],
            ['name' => 'Market Research', 'slug' => 'market-research', 'color' => '#10B981'],
            ['name' => 'Technical Analysis', 'slug' => 'technical-analysis', 'color' => '#F59E0B'],
            ['name' => 'Competitive Intelligence', 'slug' => 'competitive-intelligence', 'color' => '#EF4444'],
        ];

        foreach ($categories as $categoryData) {
            Category::create(array_merge($categoryData, ['created_by' => $user->id]));
        }

        // Create tags
        $tags = [
            ['name' => 'Bullish', 'slug' => 'bullish', 'color' => '#10B981'],
            ['name' => 'Bearish', 'slug' => 'bearish', 'color' => '#EF4444'],
            ['name' => 'Growth', 'slug' => 'growth', 'color' => '#3B82F6'],
            ['name' => 'Value', 'slug' => 'value', 'color' => '#8B5CF6'],
            ['name' => 'Dividend', 'slug' => 'dividend', 'color' => '#F59E0B'],
        ];

        foreach ($tags as $tagData) {
            Tag::create(array_merge($tagData, ['created_by' => $user->id]));
        }

        // Create companies
        $companies = [
            [
                'name' => 'Apple Inc.',
                'ticker_symbol' => 'AAPL',
                'sector' => 'Technology',
                'industry' => 'Consumer Electronics',
                'market_cap' => 3000000000000,
                'description' => 'Multinational technology company that designs consumer electronics and software.',
                'headquarters' => 'Cupertino, CA',
                'employees' => 164000,
                'founded_date' => '1976-04-01',
            ],
            [
                'name' => 'Microsoft Corporation',
                'ticker_symbol' => 'MSFT',
                'sector' => 'Technology',
                'industry' => 'Software',
                'market_cap' => 2800000000000,
                'description' => 'Technology corporation producing software, services, devices and solutions.',
                'headquarters' => 'Redmond, WA',
                'employees' => 221000,
                'founded_date' => '1975-04-04',
            ],
            [
                'name' => 'Amazon.com Inc.',
                'ticker_symbol' => 'AMZN',
                'sector' => 'Consumer Discretionary',
                'industry' => 'E-commerce',
                'market_cap' => 1500000000000,
                'description' => 'Multinational technology company focusing on e-commerce and cloud computing.',
                'headquarters' => 'Seattle, WA',
                'employees' => 1540000,
                'founded_date' => '1994-07-05',
            ],
            [
                'name' => 'Alphabet Inc.',
                'ticker_symbol' => 'GOOGL',
                'sector' => 'Communication Services',
                'industry' => 'Internet Services',
                'market_cap' => 1700000000000,
                'description' => 'Multinational conglomerate holding company specializing in internet services.',
                'headquarters' => 'Mountain View, CA',
                'employees' => 182502,
                'founded_date' => '1998-09-04',
            ],
            [
                'name' => 'Tesla Inc.',
                'ticker_symbol' => 'TSLA',
                'sector' => 'Consumer Discretionary',
                'industry' => 'Electric Vehicles',
                'market_cap' => 800000000000,
                'description' => 'Electric vehicle and clean energy company.',
                'headquarters' => 'Austin, TX',
                'employees' => 140473,
                'founded_date' => '2003-07-01',
            ],
            [
                'name' => 'NVIDIA Corporation',
                'ticker_symbol' => 'NVDA',
                'sector' => 'Technology',
                'industry' => 'Semiconductors',
                'market_cap' => 1100000000000,
                'description' => 'Technology company designing graphics processing units.',
                'headquarters' => 'Santa Clara, CA',
                'employees' => 29600,
                'founded_date' => '1993-01-29',
            ],
        ];

        foreach ($companies as $companyData) {
            Company::create(array_merge($companyData, ['created_by' => $user->id]));
        }
    }
}
