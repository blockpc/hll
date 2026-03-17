<?php

use App\Models\Roster;

uses()->group('hll', 'rosters');

it('can search rosters by name', function () {
    $matchingRoster = Roster::factory()->create([
        'name' => 'Alpha Squad',
    ]);

    Roster::factory()->create([
        'name' => 'Bravo Squad',
    ]);

    $results = Roster::query()
        ->search('Alpha')
        ->pluck('id');

    expect($results)->toHaveCount(1)
        ->and($results->first())->toBe($matchingRoster->id);
});

it('keeps roster search safe when the search term is empty', function () {
    $firstRoster = Roster::factory()->create();
    $secondRoster = Roster::factory()->create();

    $results = Roster::query()
        ->search(null)
        ->whereKey([$firstRoster->id, $secondRoster->id])
        ->pluck('id');

    expect($results)->toHaveCount(2)
        ->and($results->all())->toMatchArray([$firstRoster->id, $secondRoster->id]);
});
