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
            'slot_number' => 1,
            'role_squad_type' => null,
        ];
    }

    /**
     * Indicate that the squad soldier is a commander.
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
     * Indicate that the squad soldier is a commander.
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
