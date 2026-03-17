<?php

namespace Database\Factories;

use App\Models\CentralPoint;
use App\Models\Map;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CentralPoint>
 */
class CentralPointFactory extends Factory
{
    protected $model = CentralPoint::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'map_id' => Map::factory(),
            'name' => $this->faker->unique()->streetName(),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
