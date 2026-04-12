<?php

namespace Database\Factories;

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Squad>
 */
class SquadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array{
     *     roster_id: \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Roster>,
     *     name: string,
     *     alias: string,
     *     roster_type_squad: \App\Enums\RosterTypeSquadEnum,
     *     pos_x: int,
     *     pos_y: int,
     *     z_index: int
     * }
     */
    public function definition(): array
    {
        return [
            'roster_id' => Roster::factory(),
            'name' => $this->faker->word(),
            'alias' => $this->faker->word().'-'.$this->faker->numerify('##'),
            'roster_type_squad' => $this->faker->randomElement(RosterTypeSquadEnum::cases()),
            'pos_x' => $this->faker->numberBetween(0, 100),
            'pos_y' => $this->faker->numberBetween(0, 100),
            'z_index' => $this->faker->numberBetween(0, 10),
        ];
    }

    /**
     * Set a specific roster for the squad.
     */
    public function withRoster(Roster $roster): static
    {
        return $this->state(fn () => [
            'roster_id' => $roster->id,
        ]);
    }

    /**
     * Set a specific roster type squad enum value.
     */
    public function withTypeSquad(RosterTypeSquadEnum $typeSquad): static
    {
        return $this->state(fn () => [
            'roster_type_squad' => $typeSquad,
        ]);
    }
}
