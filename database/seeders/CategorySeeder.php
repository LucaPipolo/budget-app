<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private array $categories = [
        [
            'name' => 'Salary',
            'type' => 'income',
        ],
        [
            'name' => 'Freelance',
            'type' => 'income',
        ],
        [
            'name' => 'Investments',
            'type' => 'income',
        ],
        [
            'name' => 'Rent',
            'type' => 'outcome',
        ],
        [
            'name' => 'Groceries',
            'type' => 'outcome',
        ],
        [
            'name' => 'Transportation',
            'type' => 'outcome',
        ],
        [
            'name' => 'Healthcare',
            'type' => 'outcome',
        ],
        [
            'name' => 'Entertainment',
            'type' => 'outcome',
        ],
        [
            'name' => 'Insurance',
            'type' => 'outcome',
        ],
        [
            'name' => 'Education',
            'type' => 'outcome',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::factory()->create([
                'name' => $category['name'],
                'type' => $category['type'],
            ]);
        }
    }
}
