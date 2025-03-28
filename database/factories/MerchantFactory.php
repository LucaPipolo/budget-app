<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Merchant;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Merchant>
 */
class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'balance' => $this->faker->numberBetween(-10000, 10000),
            'team_id' => Team::inRandomOrder()->first()->id,
            'logo_path' => $this->faker->imageUrl(640, 480, 'business', true),
        ];
    }
}
