<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Models\Soldier;
use App\Models\Squad;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public Roster $roster;

    public bool $commandSquads = false;

    public string $name = 'Comandante';

    public string $alias = 'cmte';

    public ?int $selectedSoldierId = null;

    /** @var Collection<int, \App\Models\Soldier> */
    public Collection $soldiers;

    public function mount(Roster $roster, Collection $soldiers): void
    {
        $this->roster = $roster;
        $this->commandSquads = $roster->commandSquads()->exists();

        $this->soldiers = $soldiers;
    }

    public function addCommander(int $soldierId): void
    {
        $this->selectedSoldierId = $soldierId;
    }

    /**
     * Validate and create the commander squad.
     * Updates commandSquads state and closes the modal on success.
     */
    public function save(): void
    {
        $soldierKeys = $this->soldiers->keys()->toArray();
        $this->validate([
            'selectedSoldierId' => ['required', 'integer', Rule::in($soldierKeys)],
            'name' => 'required|string|max:32',
            'alias' => 'nullable|string|max:8',
        ], [], [
            'selectedSoldierId' => __('hll.squads.form.soldier'),
            'name' => __('hll.squads.form.name'),
            'alias' => __('hll.squads.form.alias'),
        ]);

        $type = 'success';
        $message = __('hll.squads.squad_command.message_success', ['name' => $this->name]);
        DB::beginTransaction();
        try {
            $squad = Squad::create([
                'roster_id' => $this->roster->id,
                'name' => $this->name,
                'alias' => $this->alias,
                'roster_type_squad' => RosterTypeSquadEnum::Commander,
            ]);

            $soldier = Soldier::find($this->selectedSoldierId);

            $squad->soldiers()->create([
                'soldier_id' => $soldier->id,
                'slot_number' => 1,
                'display_name' => $soldier->name,
            ]);

            $message = __('hll.squads.squad_command.message_success', ['name' => $soldier->name]);

            $this->commandSquads = true;

            DB::commit();

            $this->dispatch('add-squad', RosterTypeSquadEnum::Commander)->to('system::rosters.roster-template-manage');
            $this->cancelModal();
        } catch (\Throwable $th) {
            Log::error("Error al crear una escuadra de comandante. {$th->getMessage()} | {$th->getFile()} | {$th->getLine()}");
            DB::rollback();
            $type = 'error';
            $message = 'Error al crear una escuadra de comandante. Comuníquese con el administrador';
        }
    }

    public function cancelModal(): void
    {
        $this->reset('name', 'alias', 'selectedSoldierId');
        $this->clearValidation();
        $this->modal('create-squad-command')->close();
    }
};
