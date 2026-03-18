<div>
    <div class="flex items-start justify-between space-x-6">
        <x-header-clan :clan="$clan" :title="__('hll.clans.rosters.template.roster_title', ['name' => $roster->name])" :subtitle="__('hll.clans.rosters.template.subtitle')" />
        <div class="flex items-center space-x-2">
            <flux:button variant="ghost" size="sm" href="{{ route('clans.show', $clan->slug) }}">
                {{ __('hll.clans.rosters.back_to_clan') }}
            </flux:button>

            <flux:button variant="ghost" size="sm" href="{{ route('rosters.table', $clan->slug) }}">
                {{ __('hll.clans.rosters.back_to_rosters') }}
            </flux:button>
        </div>
    </div>

    <flux:separator variant="subtle" />

    <div class="flex flex-col max-h-max mt-4">
        <div class="border-dashed border-gray-300 dark:border-gray-700 flex-1">
            <div class="grid grid-cols-6 gap-4 max-h-max">
                <div class="col-span-2 border flex flex-col space-y-4 p-1">
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="text-sm italic border-b border-gray-500">Comandante</div>
                        <button class="btn btn-sm btn-default">nero</button>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="flex flex-col space-y-1 p-1">
                            <div class="text-sm italic border-b border-gray-500 flex items-center justify-between">
                                <div>Primera Infanteria </div>
                                <button class="btn btn-sm btn-default">(3/6) +</button>
                            </div>
                            <button class="btn btn-sm btn-default">xunxillo</button>
                            <button class="btn btn-sm btn-default">sebas163</button>
                            <button class="btn btn-sm btn-default">pato1910</button>
                        </div>
                        <div class="flex flex-col space-y-1 p-1">
                            <div class="text-sm italic border-b border-gray-500 flex items-center justify-between">
                                <div>Defensa lateral </div>
                                <button class="btn btn-sm btn-default">(2/6) +</button>
                            </div>
                            <button class="btn btn-sm btn-default">DonPepito</button>
                            <button class="btn btn-sm btn-default">dgo_echo</button>
                        </div>
                        <div class="flex flex-col space-y-1 p-1">
                            <div class="text-sm italic border-b border-gray-500 flex items-center justify-between">
                                <div>Segunda Infanteria</div>
                                <button class="btn btn-sm btn-default">(2/6) +</button>
                            </div>
                            <button class="btn btn-sm btn-default">mustanvr</button>
                            <button class="btn btn-sm btn-default">grayskull</button>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="text-sm italic border-b border-gray-500 flex items-center justify-between">
                            <div>Tanque Mediano</div>
                            <button class="btn btn-sm btn-default">(1/3) +</button>
                        </div>
                        <button class="btn btn-sm btn-default">cap winters</button>
                    </div>
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="text-sm italic border-b border-gray-500 flex items-center justify-between">
                            <div>Reconocimiento</div>
                            <button class="btn btn-sm btn-default">(1/2) +</button>
                        </div>
                        <button class="btn btn-sm btn-default">monty_365</button>
                    </div>
                </div>
                <div class="col-span-3 border flex flex-col space-y-4 p-1">
                    <div class="flex items-center justify-between">
                        <div class="text-sm">{{ $roster->map?->name ?? 'N/A' }}</div>
                        <div class="text-sm">{{ $roster->centralPoint?->name ?? 'N/A' }}</div>
                        <div class="text-sm">{{ $roster->faction?->label() ?? __('hll.clans.rosters.template.no_faction') }}</div>
                    </div>
                    <div>
                        <img src="{{ asset('images/mapa-hll.png') }}" class="w-full h-auto rounded">
                    </div>
                </div>
                <div class="col-span-1 border flex flex-col space-y-4 p-1">
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="text-sm italic border-b">Comandos</div>
                        @foreach ($this->typeSquads as $typeSquad)
                        <button class="btn btn-sm btn-{{ $typeSquad->color() }} disabled">{{ $typeSquad->label() }}</button>
                        @endforeach
                    </div>
                    <div class="flex flex-col space-y-1 p-1">
                        <div class="flex items-center justify-between">
                            <div class="text-sm italic">Soldados</div>
                            <div class="text-sm italic">(11/21)</div>
                        </div>
                        <div class="border-b pb-1">
                            <input type="text" class="border rounded w-full text-xs p-1 bg-black text-white dark:bg-white dark:text-black" placeholder="Buscar Soldado">
                        </div>
                        <div class="flex flex-col space-y-1 max-h-64 overflow-y-auto overscroll-y-auto">
                            <button class="btn btn-sm btn-default">papamono000</button>
                            <button class="btn btn-sm btn-default">latin</button>
                            <button class="btn btn-sm btn-default">santosmex</button>
                            <button class="btn btn-sm btn-default">manolo</button>
                            <button class="btn btn-sm btn-default">mendez</button>
                            <button class="btn btn-sm btn-default">potxibass</button>
                            <button class="btn btn-sm btn-default">daplis</button>
                            <button class="btn btn-sm btn-default">laykan</button>
                            <button class="btn btn-sm btn-default">daplis</button>
                            <button class="btn btn-sm btn-default">jeff_alfa</button>
                            <button class="btn btn-sm btn-default">hopidoggy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
