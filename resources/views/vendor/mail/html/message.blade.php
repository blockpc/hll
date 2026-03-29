<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
<div class="icon-full">
    <div class="icon-flex">
        <div class="icon-sigla">
            {{ config('app.sigla', 'HLL') }}
        </div>
        <div>
            <p class="icon-subname">
                {{ config('app.subname', 'Tactical Roster Planner') }}
            </p>
            <h1 class="icon-name">
                {{ config('app.name', 'HLL Rosters') }}
            </h1>
        </div>
    </div>
</div>
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name', 'HLL Rosters') }}. {{ __('All rights reserved.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
