<?php

use App\Services\HellLetLooseApi;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses()->group('services', 'hll-api');

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->owner = new_user(role: 'clan_owner');
    $this->helper = new_user(role: 'clan_helper');
    $this->clan = new_clan($this->owner, $this->helper);
    $this->soldier = new_soldier($this->clan, attributes: [
        'rcon' => 'playerid01',
    ]);

    $this->results = [
        'result' => [
            'id' => 1,
            'player_id' => 'playerid01',
            'sessions_count' => 33,
            'total_playtime_seconds' => 120733,
            'is_vip' => true,
            'soldier' => [
                'eos_id' => '0002489f979d43318dc0aca089d3fc8e',
                'name' => 'MYM_xunxillo',
                'level' => 42,
                'platform' => 'psn',
                'updated' => '2026-08-06T23:09:37.721729+00:00',
            ],
        ],
    ];
});

/**
 * HellLetLooseApiTest
 */
it('gets player information', function (): void {
    config()->set('services.hll.url', 'https://hll.miopesymancos.com/api');
    config()->set('services.hll.token', 'test-token');

    Http::fake([
        'https://hll.miopesymancos.com/api/get_player_profile*' => Http::response($this->results),
    ]);

    $result = app(HellLetLooseApi::class)
        ->getPlayerProfile('playerid01');

    expect($result)
        ->toBeArray()
        ->and($result['result']['soldier']['name'])->toBe('MYM_xunxillo')
        ->and($result['result']['soldier']['level'])->toBe(42);

    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && $request->url() ===
                'https://hll.miopesymancos.com/api/get_player_profile?player_id=playerid01'
            && $request->hasHeader(
                'Authorization',
                'Bearer test-token'
            );
    });
});

it('loads player information', function (): void {
    config()->set('services.hll.url', 'https://hll.miopesymancos.com/api');
    config()->set('services.hll.token', 'test-token');

    Http::fake([
        '*' => Http::response($this->results),
    ]);

    Livewire::actingAs($this->owner)
        ->test('system::clans.soldiers-manager', ['clan' => $this->clan])
        ->call('getPlayerProfile', $this->soldier->id)
        ->assertSet('playerProfile.result.soldier.name', 'MYM_xunxillo')
        ->assertSet('playerProfile.result.soldier.level', 42)
        ->assertDispatched('refresh-soldiers-manager');
});

it('shows alert and keeps state when profile API returns error response', function (): void {
    config()->set('services.hll.url', 'https://hll.miopesymancos.com/api');
    config()->set('services.hll.token', 'test-token');

    Http::fake([
        '*' => Http::response(['message' => 'Server Error'], 500),
    ]);

    Livewire::actingAs($this->owner)
        ->test('system::clans.soldiers-manager', ['clan' => $this->clan])
        ->call('getPlayerProfile', $this->soldier->id)
        ->assertSet('playerProfile', [])
        ->assertDispatched('show')
        ->assertNotDispatched('refresh-soldiers-manager');
});

it('shows alert and keeps state when profile API connection fails', function (): void {
    config()->set('services.hll.url', 'https://hll.miopesymancos.com/api');
    config()->set('services.hll.token', 'test-token');

    Http::fake([
        '*' => Http::failedConnection(),
    ]);

    Livewire::actingAs($this->owner)
        ->test('system::clans.soldiers-manager', ['clan' => $this->clan])
        ->call('getPlayerProfile', $this->soldier->id)
        ->assertSet('playerProfile', [])
        ->assertDispatched('show')
        ->assertNotDispatched('refresh-soldiers-manager');
});
