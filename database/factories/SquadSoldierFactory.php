<?php

namespace Database\Factories;

use App\Models\Soldier;
use App\Models\Squad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SquadSoldier>
 */
class SquadSoldierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'squad_id' => Squad::factory(),
            'soldier_id' => Soldier::factory(),
            'display_name' => $this->faker->word(),
            'slot_number' => $this->faker->numberBetween(1, 10),
            'role_squad_type' => null,
        ];
    }

    /**
     * Associate the squad soldier with a specific squad.
     *
     * @return static
     */
    public function withRoster(Squad $squad): static
    {
        return $this->state(fn () => [
            'squad_id' => $squad->id,
        ]);
    }

    /**
     * Associate the squad soldier with a specific soldier.
     *
     * @return static
     */
    public function withSoldier(Soldier $soldier): static
    {
        return $this->state(fn () => [
            'soldier_id' => $soldier->id,
            'display_name' => $soldier->name,
        ]);
    }
}
