<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Company permissions
            'create companies',
            'view companies',
            'update companies',
            'delete companies',

            // Research Items permissions
            'create research items',
            'view research items',
            'update research items',
            'delete research items',

            // Category permissions
            'create categories',
            'view categories',
            'update categories',
            'delete categories',

            // Tag permissions
            'create tags',
            'view tags',
            'update tags',
            'delete tags',

            // Asset permissions
            'create assets',
            'view assets',
            'update assets',
            'delete assets',

            // Admin permissions
            'admin access',
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $adminRole->syncPermissions($permissions);

        // Assign basic permissions to user role
        $userRole->syncPermissions([
            'view companies',
            'view research items',
            'view categories',
            'view tags',
            'view assets',
        ]);

        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->assignRole('user');

        // Create shawn admin user
        $adminUser = User::factory()->create([
            'name' => 'Shawn',
            'email' => 'shawn@roguespy.co',
            'password' => bcrypt('kngfqp57'),
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        // Create categories
        $categories = [
            ['name' => 'Financial Analysis', 'slug' => 'financial-analysis', 'color' => '#3B82F6'],
            ['name' => 'Market Research', 'slug' => 'market-research', 'color' => '#10B981'],
            ['name' => 'Technical Analysis', 'slug' => 'technical-analysis', 'color' => '#F59E0B'],
            ['name' => 'Competitive Intelligence', 'slug' => 'competitive-intelligence', 'color' => '#EF4444'],
        ];

        foreach ($categories as $categoryData) {
            Category::create(array_merge($categoryData, ['created_by' => $adminUser->id]));
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
            Tag::create(array_merge($tagData, ['created_by' => $adminUser->id]));
        }

        // Create companies
        $companies = [
            [
                'name' => 'Apple Inc.',
                'ticker' => 'AAPL',
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
                'ticker' => 'MSFT',
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
                'ticker' => 'AMZN',
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
                'ticker' => 'GOOGL',
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
                'ticker' => 'TSLA',
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
                'ticker' => 'NVDA',
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
            Company::create(array_merge($companyData, ['created_by' => $adminUser->id]));
        }
    }
}
