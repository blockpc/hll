<?php

use Blockpc\App\Rules\AreEqualsRule;
use Illuminate\Support\Facades\Validator;

uses()->group('blockpc', 'rules');

it('the rule passes when values are equal', function () {
    $validator = Validator::make(
        ['name' => 'test'],
        ['name' => [new AreEqualsRule('test')]],
    );

    expect($validator->fails())->toBeFalse();
});

it('the rule fails when values differ', function () {
    $validator = Validator::make(
        ['name' => 'other'],
        ['name' => [new AreEqualsRule('test')]],
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('name'))->toBe('The name is not equal to value.');
});

it('the rule fails when comparison text is null', function () {
    $validator = Validator::make(
        ['name' => 'test'],
        ['name' => [new AreEqualsRule(null)]],
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('name'))->toBe('The text to compare is null.');
});
