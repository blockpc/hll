<?php

namespace Database\Factories;

use App\Enums\FactionTypeEnum;
use App\Models\CentralPoint;
use App\Models\Clan;
use App\Models\Map;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Roster>
 */
class RosterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clan = Clan::query()->first() ?? Clan::factory()->create();
        $map = Map::query()->inRandomOrder()->first() ?? Map::factory()->create();
        $centralPoint = $map->centralPoints()->inRandomOrder()->first()
            ?? CentralPoint::factory()->for($map)->create();

        return [
            'clan_id' => $clan->id,
            'name' => $this->faker->text(50),
            'description' => $this->faker->sentence(),
            'faction' => $this->faker->randomElement(FactionTypeEnum::cases()),
            'max_soldiers' => $this->faker->randomElement([30, 40, 50]),
            'map_id' => $map->id,
            'central_point_id' => $centralPoint->id,
            'image' => null,
            'is_public' => false,
            'multiclan' => false,
            'multifaction' => false,
        ];
    }
}
