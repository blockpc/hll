<?php

use App\Http\Requests\ClanCreateRequest;
use App\Livewire\Notifications\Traits\Select2UsersNotificationsTrait;
use App\Models\Clan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use Select2UsersNotificationsTrait, WithFileUploads;

    public string $alias = '';

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public string $discord = '';

    public ?int $owner_user_id = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $logo;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $image;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('create', Clan::class), 403, __('hll.clans.403'));
    }

    public function save(): RedirectResponse|Redirector|null
    {
        abort_unless(auth()->user()->can('create', Clan::class), 403, __('hll.clans.403'));

        if ($this->canSelectOwner() && $this->selectedUserId > 0) {
            $this->owner_user_id = $this->selectedUserId;
        }

        $data = $this->validate();

        $owner = auth()->user()->hasRole('clan_owner')
            ? auth()->user()
            : User::findOrFail($data['owner_user_id']);

        $ownerUserId = $owner->id;

        $clan = DB::transaction(function () use ($data, $ownerUserId) {
            $owner = User::query()
                ->whereKey($ownerUserId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($owner->hasRole('clan_owner') && $owner->ownedClan()->exists()) {
                return null;
            }

            if ($owner->ownedClan()->exists()) {
                return null;
            }

            $clan = Clan::create([
                'owner_user_id' => $ownerUserId,
                'alias' => $data['alias'],
                'name' => $data['name'],
                'slug' => $data['slug'] ?? $this->slug,
                'description' => $data['description'] ?: null,
                'discord_url' => $data['discord'] ?: null,
            ]);

            if ($this->logo) {
                $clan->logo = $this->logo->store('clans/'.$clan->alias, 'public');
            }

            if ($this->image) {
                $clan->image = $this->image->store('clans/'.$clan->alias, 'public');
            }

            $clan->save();

            if (! $owner->hasRole('clan_owner')) {
                $owner->assignRole('clan_owner');
            }

            $clan->members()->attach($ownerUserId, ['membership_role' => 'owner']);

            return $clan;
        });

        if (! $clan) {
            $this->addError('owner_user_id', __('hll.clans.create.error_owner_already_has_clan'));

            return null;
        }

        return redirect()->route('clans.show', ['clan' => $clan->slug]);
    }

    public function canSelectOwner(): bool
    {
        return ! auth()->user()->hasRole('clan_owner') && auth()->user()->can('clans.create');
    }

    protected function rules(): array
    {
        $rules = (new ClanCreateRequest)->rules();

        if ($this->canSelectOwner()) {
            $rules['owner_user_id'] = ['required', 'integer', Rule::exists('users', 'id'), Rule::notIn([auth()->id()])];
        }

        return $rules;
    }

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
    }
};
