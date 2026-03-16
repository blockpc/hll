<?php

use App\Enums\ClanMembershipRoleEnum;
use Tests\TestCase;

uses(TestCase::class);

test('it resolves owner and helper labels in english locale', function () {
    app('translator')->setLocale('en');

    expect(ClanMembershipRoleEnum::Owner->label())->toBe('Owner')
        ->and(ClanMembershipRoleEnum::Helper->label())->toBe('Helper');
});
