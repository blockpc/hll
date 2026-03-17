<?php

use App\Models\Map;
use Database\Seeders\MapSeeder;

uses()->group('hll');

// CheckMapSeederTest

it('seeds maps with their central points', function () {
    $this->seed(MapSeeder::class);

    expect(Map::count())->toBe(19);

    Map::with('centralPoints')->get()->each(function ($map) {
        expect($map->centralPoints)->toHaveCount(3);
    });
});
