<?php

namespace Database\Factories;

use App\Models\Clan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Soldier>
 */
class SoldierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clan_id' => Clan::factory(),
            'name' => $this->faker->unique()->firstName(),
            'role' => null,
            'observation' => null,
        ];
    }

    /**
     * Configure the factory to use a specific clan.
     */
    public function forClan(Clan $clan): static
    {
        return $this->state(fn () => ['clan_id' => $clan->id]);
    }
}
