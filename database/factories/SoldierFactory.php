<?php

namespace Database\Factories;

use App\Enums\RoleSquadTypeEnum;
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
            'role' => $this->faker->randomElement(RoleSquadTypeEnum::cases()),
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

    /**
     * Configure the factory to create a soldier without a role.
     */
    public function withoutRole(): static
    {
        return $this->state(fn () => ['role' => null]);
    }
}
