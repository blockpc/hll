<div class="w-full">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @if ($squadCommander)
            <x-squad :squad="$squadCommander" :buttons="true" />
        @endif
        <div></div>
    </div>
</div>
