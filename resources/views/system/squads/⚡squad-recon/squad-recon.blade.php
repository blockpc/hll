<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @foreach ($roster->reconSquads as $customSquad)
        <x-squad :squad="$customSquad" :buttons="$buttons" />
        @endforeach
    </div>
</div>
