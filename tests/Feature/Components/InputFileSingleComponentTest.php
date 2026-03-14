<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ViewErrorBag;

test('input file single uses horizontal layout by default', function () {
    $html = Blade::render(
        '<x-input-file-single name="logo" wire:model="logo"><div>Preview</div></x-input-file-single>',
        ['errors' => new ViewErrorBag]
    );

    expect($html)->toContain('items-center justify-start space-x-4');
    expect($html)->toContain('wire:loading.block');
    expect($html)->toContain('block h-2 w-full rounded accent-blue-500');
    expect($html)->not->toContain('flex-col items-center space-y-4');
});

test('input file single can render stacked layout', function () {
    $html = Blade::render(
        '<x-input-file-single name="logo" wire:model="logo" stacked><div>Preview</div></x-input-file-single>',
        ['errors' => new ViewErrorBag]
    );

    expect($html)->toContain('flex-col items-center space-y-4');
    expect($html)->toContain('text-center');
    expect($html)->not->toContain('items-center justify-start space-x-4');
});
