<?php

use App\Enums\RosterTypeSquadEnum;
use App\Models\Roster;
use App\Services\CreateCommanderSquadService;
use Blockpc\Traits\AlertBrowserEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;

    public Roster $roster;

    public bool $commandSquads = false;

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
            'alias' => 'nullable|string|max:8',
        ], [], [
            'selectedSoldierId' => __('hll.squads.form.soldier'),
            'alias' => __('hll.squads.form.alias'),
        ]);

        $type = 'success';
        $message = __('hll.squads.squad_command.success_message', ['name' => __('hll.roster_type_squad.commander')]);

        try {
            $squad = $this->createCommanderSquadService()->create(
                $this->roster,
                (int) $this->selectedSoldierId,
                __('hll.roster_type_squad.commander'),
                $this->alias,
            );

            $message = __('hll.squads.squad_command.success_message', ['name' => $squad->soldiers()->first()?->display_name ?? $this->name]);

            $this->commandSquads = true;

            $this->dispatch('add-squad', RosterTypeSquadEnum::Commander)->to('system::rosters.roster-template-manage');
            $this->cancelModal();
        } catch (\DomainException $exception) {
            $type = 'error';
            $message = $exception->getMessage();
        } catch (\Throwable $th) {
            Log::error("Error al crear una escuadra de comandante. {$th->getMessage()} | {$th->getFile()} | {$th->getLine()}");
            $type = 'error';
            $message = __('hll.squads.squad_command.error_message');
        }

        $this->alert($message, $type, __('hll.squads.squad_command.title'));
    }

    public function cancelModal(): void
    {
        $this->reset('alias', 'selectedSoldierId');
        $this->clearValidation();
        $this->modal('create-squad-command')->close();
    }

    private function createCommanderSquadService(): CreateCommanderSquadService
    {
        return app(CreateCommanderSquadService::class);
    }
};
