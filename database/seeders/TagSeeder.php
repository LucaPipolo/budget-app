<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    private array $tagNames = [
        'to split', 'check', 'holidays', 'recurring', 'one-time', 'urgent',
        'savings goal', 'kids', 'shared expense', 'reimbursable',
        'tax deductible', 'needs review', 'debt payment',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->tagNames as $tagName) {
            Tag::factory()->create([
                'name' => $tagName,
            ]);
        }
    }
}
