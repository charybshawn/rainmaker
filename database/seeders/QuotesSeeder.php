<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create one for quotes
        $admin = User::where('email', 'admin@example.com')->first() ?? User::first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'System Admin',
                'email' => 'admin@rainmaker.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $quotes = [
            [
                'title' => 'Warren Buffett on Time and Investing',
                'content' => 'Someone\'s sitting in the shade today because someone planted a tree a long time ago.',
                'author_name' => 'Warren Buffett',
            ],
            [
                'title' => 'Benjamin Graham on Market Behavior',
                'content' => 'In the short run, the market is a voting machine but in the long run, it is a weighing machine.',
                'author_name' => 'Benjamin Graham',
            ],
            [
                'title' => 'Peter Lynch on Investment Knowledge',
                'content' => 'Know what you own, and know why you own it.',
                'author_name' => 'Peter Lynch',
            ],
            [
                'title' => 'Warren Buffett on Risk Management',
                'content' => 'Risk comes from not knowing what you\'re doing.',
                'author_name' => 'Warren Buffett',
            ],
            [
                'title' => 'John Templeton on Market Opportunity',
                'content' => 'The time of maximum pessimism is the best time to buy, and the time of maximum optimism is the best time to sell.',
                'author_name' => 'John Templeton',
            ],
            [
                'title' => 'Charlie Munger on Learning',
                'content' => 'I constantly see people rise in life who are not the smartest, sometimes not even the most diligent, but they are learning machines.',
                'author_name' => 'Charlie Munger',
            ],
            [
                'title' => 'Benjamin Franklin on Investment',
                'content' => 'An investment in knowledge pays the best interest.',
                'author_name' => 'Benjamin Franklin',
            ],
            [
                'title' => 'George Soros on Market Psychology',
                'content' => 'It\'s not whether you\'re right or wrong that\'s important, but how much money you make when you\'re right and how much you lose when you\'re wrong.',
                'author_name' => 'George Soros',
            ],
            [
                'title' => 'Ray Dalio on Principles',
                'content' => 'He who lives by the crystal ball will eat shattered glass.',
                'author_name' => 'Ray Dalio',
            ],
            [
                'title' => 'Carl Icahn on Opportunity',
                'content' => 'In life and business, there are two cardinal sins. The first is to act precipitously without thought and the second is to not act at all.',
                'author_name' => 'Carl Icahn',
            ],
        ];

        foreach ($quotes as $quoteData) {
            BlogPost::create([
                'title' => $quoteData['title'],
                'slug' => Str::slug($quoteData['title']),
                'content' => $quoteData['content'],
                'category' => 'quotes',
                'author_name' => $quoteData['author_name'],
                'status' => 'published',
                'published_at' => now(),
                'user_id' => $admin->id,
            ]);
        }
    }
}