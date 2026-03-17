<?php

namespace Database\Factories;

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
        $clan = Clan::query()->first();
        $map = Map::query()->inRandomOrder()->first();
        $centralPoint = CentralPoint::query()->where('map_id', $map->id)->inRandomOrder()->first();

        return [
            'clan_id' => $clan->id,
            'name' => $this->faker->text(50),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'faction' => $this->faker->randomElement(['allies', 'axis']),
            'map_id' => $map->id,
            'central_point_id' => $centralPoint->id,
            'image' => null,
            'is_public' => false,
            'multiclan' => false,
        ];
    }
}
