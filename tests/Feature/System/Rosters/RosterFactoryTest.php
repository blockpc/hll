<?php

use App\Models\Roster;

uses()->group('hll', 'rosters');

it('creates missing clan map and central point dependencies when using roster factory', function () {
    $roster = Roster::factory()->create();

    expect($roster->clan)->not()->toBeNull();
    expect($roster->map)->not()->toBeNull();
    expect($roster->centralPoint)->not()->toBeNull();
    expect($roster->centralPoint->map_id)->toBe($roster->map_id);
});
