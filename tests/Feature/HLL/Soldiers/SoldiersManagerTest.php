<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Enums\RoleSquadTypeEnum;
use App\Enums\RosterTypeSquadEnum;
use App\Models\Soldier;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses()->group('hll');

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = new_user();
});

// SoldiersManagerTest

it('check properties in livewire component', function () {
    $clan = new_clan($this->user);

    Livewire::actingAs($this->user)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertSet('name', '')
        ->assertSet('role', null)
        ->assertSet('observation', null)
        ->assertSet('bulkNames', null);
});

it('only clan_owner and clan_helper can access to add soldiers', function () {
    $owner = new_user(role: 'clan_owner');
    $helper = new_user(role: 'clan_helper');
    $otherUser = new_user();

    $clan = new_clan($owner);
    $clan->members()->attach($helper->id, ['membership_role' => ClanMembershipRoleEnum::Helper->value]);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertSet('name', '')
        ->assertSet('role', null)
        ->assertSet('observation', null);

    Livewire::actingAs($helper)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertSet('name', '')
        ->assertSet('role', null)
        ->assertSet('observation', null);

    Livewire::actingAs($otherUser)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->assertStatus(403);
});

it('creates a soldier with optional role and observation', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->set('role', RoleSquadTypeEnum::Rifleman->value)
        ->set('observation', 'Test observation')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);

    $soldier = $clan->soldiers()->first();
    expect($soldier->name)->toBe('Alpha');
    expect($soldier->role)->toBe(RoleSquadTypeEnum::Rifleman);
    expect($soldier->observation)->toBe('Test observation');
});

it('validates required soldier name', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('validates squad role enum when provided', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->set('role', 'invalid_role')
        ->call('save')
        ->assertHasErrors(['role']);
});

it('prevents duplicate soldier names in the same clan', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    Soldier::factory()->forClan($clan)->create(['name' => 'Alpha']);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('allows duplicate soldier names in different clans', function () {
    $owner1 = new_user(role: 'clan_owner');
    $owner2 = new_user(role: 'clan_owner');
    $clan1 = new_clan($owner1);
    $clan2 = new_clan($owner2);
    Soldier::factory()->forClan($clan1)->create(['name' => 'Alpha']);

    Livewire::actingAs($owner2)
        ->test('system::clans.soldiers-manager', ['clan' => $clan2])
        ->set('name', 'Alpha')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan2->soldiers()->count())->toBe(1);
});

it('creates many soldiers from comma or newline separated input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('bulkNames', "Alpha, Beta\nGamma")
        ->set('manySoldiers', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(3);
    expect($clan->soldiers()->pluck('name')->sort()->values()->toArray())
        ->toBe(['Alpha', 'Beta', 'Gamma']);
});

it('stores null role and observation for bulk created soldiers', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha, Beta')
        ->call('save');

    expect($clan->soldiers()->count())->toBe(2);
    expect($clan->soldiers()->whereNull('role')->whereNull('observation')->count())->toBe(2);
});

it('ignores empty values in bulk input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha,,Beta,')
        ->call('save');

    expect($clan->soldiers()->count())->toBe(2);
});

it('ignores duplicated names inside the same bulk input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha, Alpha, Beta')
        ->call('save');

    expect($clan->soldiers()->count())->toBe(2);
});

it('forbids managing soldiers for a clan the user does not own', function () {
    $owner1 = new_user(role: 'clan_owner');
    $owner2 = new_user(role: 'clan_owner');
    $clan1 = new_clan($owner1);
    new_clan($owner2); // owner2 has their own clan, but shouldn't access clan1

    Livewire::actingAs($owner2)
        ->test('system::clans.soldiers-manager', ['clan' => $clan1])
        ->assertStatus(403);
});

it('the soldier name must not be saved in lowercase', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Alpha')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);

    $soldier = $clan->soldiers()->first();
    expect($soldier->name)->toBe('Alpha');
});

it('the soldier name must be saved without accents', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('name', 'Álphá')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);

    $soldier = $clan->soldiers()->first();
    expect($soldier->name)->toBe('Alpha');
});

it('can edit a soldier', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    $soldier = Soldier::factory()->forClan($clan)->create(['name' => 'Alpha', 'role' => RoleSquadTypeEnum::Rifleman, 'observation' => 'Initial observation']);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->call('showEditSoldier', $soldier->id)
        ->set('soldier_name', 'Bravo')
        ->set('soldier_role', RoleSquadTypeEnum::Medic->value)
        ->set('soldier_observation', 'Updated observation')
        ->call('editSoldier')
        ->assertHasNoErrors();

    $soldier->refresh();
    expect($soldier->name)->toBe('Bravo');
    expect($soldier->role)->toBe(RoleSquadTypeEnum::Medic);
    expect($soldier->observation)->toBe('Updated observation');
});

it('preserves historical squad pivot rows when a soldier is deleted', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    $roster = new_roster($clan, ['max_soldiers' => 20]);
    $firstSquad = new_squad($roster, RosterTypeSquadEnum::Custom);
    $secondSquad = new_squad($roster, RosterTypeSquadEnum::Custom);
    $soldier = new_soldier($clan, $firstSquad, ['name' => 'Alpha']);
    $soldier->load('squads');

    add_soldier_to_squad($secondSquad, $soldier);

    $soldier->delete();

    $this->assertDatabaseHas('squad_soldiers', [
        'squad_id' => $firstSquad->id,
        'soldier_id' => null,
        'display_name' => 'Alpha',
        'slot_number' => 1,
    ]);

    $this->assertDatabaseHas('squad_soldiers', [
        'squad_id' => $secondSquad->id,
        'soldier_id' => null,
        'display_name' => 'Alpha',
        'slot_number' => 1,
    ]);
});

it('can delete a soldier', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    $soldier = Soldier::factory()->forClan($clan)->create(['name' => 'Alpha', 'role' => RoleSquadTypeEnum::Rifleman, 'observation' => 'Initial observation']);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->call('showDeleteSoldier', $soldier->id)
        ->set('current_name', 'Alpha')
        ->call('deleteSoldier')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(0);
});

it('skips names exceeding 32 characters in bulk input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    $longName = str_repeat('a', 33);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', "Alpha, {$longName}")
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);
    expect($clan->soldiers()->first()->name)->toBe('Alpha');
});

it('tracks names already existing in the clan as duplicates in bulk input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);
    Soldier::factory()->forClan($clan)->create(['name' => 'alpha']);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha, Beta')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(2);
    expect($clan->soldiers()->pluck('name')->sort()->values()->toArray())->toContain('alpha', 'Beta');
});

it('skips whitespace-only segments in bulk input', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('manySoldiers', true)
        ->set('bulkNames', 'Alpha, , Beta')
        ->call('save')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(2);
});

it('exports soldiers with clan-specific csv content', function () {
    $ownerOne = new_user(role: 'clan_owner');
    $ownerTwo = new_user(role: 'clan_owner');

    $clanOne = new_clan($ownerOne);
    $clanTwo = new_clan($ownerTwo);

    $clanOneSoldierOne = Soldier::factory()->forClan($clanOne)->create([
        'name' => 'Alpha',
        'role' => RoleSquadTypeEnum::Rifleman,
        'observation' => 'First clan note',
    ]);
    $clanOneSoldierTwo = Soldier::factory()->forClan($clanOne)->create([
        'name' => 'Bravo',
        'role' => null,
        'observation' => null,
    ]);

    $clanTwoSoldier = Soldier::factory()->forClan($clanTwo)->create([
        'name' => 'Zulu',
        'role' => RoleSquadTypeEnum::Medic,
        'observation' => 'Second clan note',
    ]);

    $buildCsv = static function (array $rows): string {
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $csv;
    };

    $expectedClanOneCsv = $buildCsv([
        ['name', 'role', 'observation'],
        ['Alpha', 'rifleman', 'First clan note'],
        ['Bravo', '', ''],
    ]);

    $expectedClanTwoCsv = $buildCsv([
        ['name', 'role', 'observation'],
        ['Zulu', 'medic', 'Second clan note'],
    ]);

    Livewire::actingAs($ownerOne)
        ->test('system::clans.soldiers-manager', ['clan' => $clanOne])
        ->call('exportSoldiers')
        ->assertFileDownloaded($clanOne->slug.'-soldiers.csv', $expectedClanOneCsv);

    Livewire::actingAs($ownerTwo)
        ->test('system::clans.soldiers-manager', ['clan' => $clanTwo])
        ->call('exportSoldiers')
        ->assertFileDownloaded($clanTwo->slug.'-soldiers.csv', $expectedClanTwoCsv);

    expect($expectedClanOneCsv)->not->toBe($expectedClanTwoCsv);
});

it('escapes formula-like values in exported soldier name and observation', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Soldier::factory()->forClan($clan)->create([
        'name' => '=Alpha',
        'role' => RoleSquadTypeEnum::Rifleman,
        'observation' => '+Dangerous',
    ]);

    Soldier::factory()->forClan($clan)->create([
        'name' => 'Bravo',
        'role' => RoleSquadTypeEnum::Medic,
        'observation' => 'Safe text',
    ]);

    $buildCsv = static function (array $rows): string {
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $csv;
    };

    $expectedCsv = $buildCsv([
        ['name', 'role', 'observation'],
        ["'=Alpha", 'rifleman', "'+Dangerous"],
        ['Bravo', 'medic', 'Safe text'],
    ]);

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->call('exportSoldiers')
        ->assertFileDownloaded($clan->slug.'-soldiers.csv', $expectedCsv);
});

it('rejects import when csv header is not exact', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    $csv = "name,observation,role\nAlpha,Note,rifleman\n";

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('importFile', UploadedFile::fake()->createWithContent('soldiers.csv', $csv))
        ->call('import')
        ->assertHasErrors(['importFile']);

    expect($clan->soldiers()->count())->toBe(0);
});

it('rolls back import when any row column count differs from header', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    $csv = "name,role,observation\nAlpha,rifleman,First\nBravo,medic\n";

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('importFile', UploadedFile::fake()->createWithContent('soldiers.csv', $csv))
        ->call('import')
        ->assertHasErrors(['importFile']);

    expect($clan->soldiers()->count())->toBe(0);
});

it('rolls back import when any row has missing name or invalid enum role', function () {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    $csvMissingName = "name,role,observation\nAlpha,rifleman,First\n,medic,Second\n";

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('importFile', UploadedFile::fake()->createWithContent('soldiers-missing-name.csv', $csvMissingName))
        ->call('import')
        ->assertHasErrors(['importFile']);

    expect($clan->soldiers()->count())->toBe(0);

    $csvInvalidRole = "name,role,observation\nAlpha,rifleman,First\nBravo,invalid_role,Second\n";

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('importFile', UploadedFile::fake()->createWithContent('soldiers-invalid-role.csv', $csvInvalidRole))
        ->call('import')
        ->assertHasErrors(['importFile']);

    expect($clan->soldiers()->count())->toBe(0);
});

it('imports soldiers successfully with normalized names and updates existing records', function (): void {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    Soldier::factory()->forClan($clan)->create([
        'name' => 'Alpha',
        'role' => RoleSquadTypeEnum::Rifleman,
        'observation' => 'Antiguo',
    ]);

    $csv = "name,role,observation\nÁlphá,medic,Actualizado\nBravó,rifleman,Nuevo\n";

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('importFile', UploadedFile::fake()->createWithContent('soldiers-normalized-name.csv', $csv))
        ->call('import')
        ->assertHasNoErrors()
        ->assertSet('importFile', null)
        ->assertDispatched('show');

    expect($clan->soldiers()->count())->toBe(2);

    $alpha = $clan->soldiers()->where('name', 'Alpha')->first();
    $bravo = $clan->soldiers()->where('name', 'Bravo')->first();

    expect($alpha)->not->toBeNull()
        ->and($alpha?->role)->toBe(RoleSquadTypeEnum::Medic)
        ->and($alpha?->observation)->toBe('Actualizado')
        ->and($bravo)->not->toBeNull()
        ->and($bravo?->role)->toBe(RoleSquadTypeEnum::Rifleman)
        ->and($bravo?->observation)->toBe('Nuevo');
});

it('ignores duplicate normalized names within the same import file', function (): void {
    $owner = new_user(role: 'clan_owner');
    $clan = new_clan($owner);

    $csv = "name,role,observation\nÁlphá,rifleman,Primero\nAlpha,medic,Segundo\nALPHA,medic,Tercero\n";

    Livewire::actingAs($owner)
        ->test('system::clans.soldiers-manager', ['clan' => $clan])
        ->set('importFile', UploadedFile::fake()->createWithContent('soldiers-duplicate-normalized-name.csv', $csv))
        ->call('import')
        ->assertHasNoErrors();

    expect($clan->soldiers()->count())->toBe(1);

    $alpha = $clan->soldiers()->where('name', 'Alpha')->first();

    expect($alpha)->not->toBeNull()
        ->and($alpha?->role)->toBe(RoleSquadTypeEnum::Rifleman)
        ->and($alpha?->observation)->toBe('Primero');
});
