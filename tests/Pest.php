<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use App\Enums\RosterTypeSquadEnum;
use App\Models\Clan;
use App\Models\Roster;
use App\Models\Soldier;
use App\Models\Squad;
use App\Models\SquadSoldier;
use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function new_user(?string $role = null): User
{
    $user = User::factory()->create();

    if ($role) {
        $user->assignRole($role);
    }

    return $user;
}

function new_clan(?User $owner = null, ?User $helper = null, array $attributes = []): Clan
{
    if ($helper && ! $owner) {
        throw new \InvalidArgumentException('Cannot assign a helper without an owner.');
    }

    if ($owner && ! $helper) {
        $owner->assignRole('clan_owner');

        return Clan::factory()->withOwner($owner)->create($attributes);
    }

    if ($owner && $helper) {
        $owner->assignRole('clan_owner');
        $helper->assignRole('clan_helper');

        return Clan::factory()->withOwner($owner)->withHelper($helper)->create($attributes);
    }

    return Clan::factory()->create($attributes);
}

function new_roster(Clan $clan, array $attributes = []): Roster
{
    return Roster::factory()->for($clan)->create($attributes);
}

function new_squad(Roster $roster, ?RosterTypeSquadEnum $rosterTypeSquad = null, array $attributes = []): Squad
{
    if ($rosterTypeSquad && ! in_array($rosterTypeSquad, RosterTypeSquadEnum::cases())) {
        throw new \InvalidArgumentException('Invalid roster type squad.');
    }

    if ($rosterTypeSquad) {
        $attributes['roster_type_squad'] = $rosterTypeSquad->value;
    }

    return Squad::factory()->for($roster)->create($attributes);
}

function new_soldier(Clan $clan, ?Squad $squad = null, array $attributes = []): Soldier
{
    $soldier = Soldier::factory()->forClan($clan)->create($attributes);

    if ($squad) {
        $nextSlot = $squad->squadSoldiers()->max('slot_number') + 1;

        $squad->squadSoldiers()->create([
            'soldier_id' => $soldier->id,
            'display_name' => $soldier->name,
            'slot_number' => $nextSlot,
        ]);
    }

    return $soldier;
}

function add_soldier_to_squad(Soldier $soldier, ?Squad $squad = null, ?string $onlyName, ?int $slot = null): SquadSoldier
{
    $squadSoldier = null;

    if (! $squad->exists() && ! $onlyName) {
        throw new \InvalidArgumentException('Cannot add soldier without a squad or a name.');
    }

    $nextSlot = $slot ?? $squad->squadSoldiers()->max('slot_number') + 1;

    if ($squad->exists()) {
        $squadSoldier = $squad->squadSoldiers()->create([
            'soldier_id' => $soldier->id,
            'display_name' => $soldier->name,
            'slot_number' => $nextSlot,
        ]);
    }

    if ($onlyName) {
        $squadSoldier = $squad->squadSoldiers()->create([
            'display_name' => $onlyName,
            'slot_number' => $nextSlot,
        ]);
    }

    return $squadSoldier;
}
