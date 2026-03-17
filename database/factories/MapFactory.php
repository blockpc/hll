<?php

namespace Database\Factories;

use App\Models\Map;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Map>
 */
class MapFactory extends Factory
{
    protected $model = Map::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alias' => Str::lower(Str::random(8)),
            'name' => $this->faker->unique()->city(),
            'timeline' => $this->faker->optional()->word(),
            'location' => $this->faker->optional()->country(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
