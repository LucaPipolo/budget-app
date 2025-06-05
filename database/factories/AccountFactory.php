<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'type' => $this->faker->randomElement(['bank', 'cash', 'investments']),
            'origin' => $this->faker->randomElement(['web', 'api', 'external']),
            'logo_path' => $this->faker->imageUrl(640, 480, 'business'),
            'balance' => 0,
            // We are excluding SLL (Sierra Leonean Leone) as it is wrongly still included
            // in Faker currency codes but not anymore in ISO 4217.
            // There is a PR to fix this in Faker, but it is not included in the latest release (1.24.1).
            // @see https://github.com/FakerPHP/Faker/issues/919
            // @see https://github.com/FakerPHP/Faker/pull/920
            'currency' => $this->faker->valid(fn ($code) => $code !== 'SLL')->currencyCode(),
            'iban' => $this->faker->iban('ES'),
            'swift' => $this->faker->swiftBicNumber(),
            'team_id' => Team::inRandomOrder()->first()->id,

            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'updated_at' => $this->faker->dateTimeBetween('-1 week'),
        ];
    }
}
