<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Tag;
use App\Models\Team;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => random_int(1, 100) <= 20 ?
                $this->faker->biasedNumberBetween(50000, 250000) :
                $this->faker->biasedNumberBetween(-25000, -1),
            'date' => $this->faker->dateTimeBetween('-1 year'),
            'notes' => $this->faker->optional(0.2)->sentence(),
            'account_id' => Account::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'merchant_id' => Merchant::inRandomOrder()->first()->id,
            'team_id' => Team::inRandomOrder()->first()->id,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return TransactionFactory The configured factory instance.
     */
    public function configure(): TransactionFactory
    {
        return $this->afterCreating(function (Transaction $transaction): void {
            Tag::all()
                ->whenNotEmpty(fn ($tags) => $tags->random(min($tags->count(), random_int(0, 2))))
                ->each(fn ($tag) => $transaction->tags()->attach($tag->id));
        });
    }
}
