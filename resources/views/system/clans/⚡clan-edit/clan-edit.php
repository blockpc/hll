<?php

use App\Http\Requests\ClanUpdateRequest;
use App\Livewire\Notifications\Traits\Select2UsersNotificationsTrait;
use App\Models\Clan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use Select2UsersNotificationsTrait, WithFileUploads;

    public Clan $clan;

    public string $alias = '';

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public ?string $discord = null;

    public ?int $owner_user_id = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $logo;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $image;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('update', $this->clan), 403, __('hll.clans.403'));

        $this->alias = $this->clan->alias;
        $this->name = $this->clan->name;
        $this->slug = $this->clan->slug;
        $this->description = $this->clan->description;
        $this->discord = $this->clan->discord_url;
        $this->owner_user_id = $this->clan->owner_user_id;
    }

    public function save(): RedirectResponse|Redirector|null
    {
        abort_unless(auth()->user()->can('update', $this->clan), 403, __('hll.clans.403'));

        $data = $this->validate();

        DB::transaction(function () use ($data) {
            $updateData = [
                'alias' => $data['alias'],
                'name' => $data['name'],
                'slug' => $data['slug'] ?: null,
                'description' => $data['description'] ?: null,
                'discord_url' => $data['discord'] ?: null,
            ];

            if ($this->logo) {
                $newLogoPath = $this->logo->store('clans/'.$data['alias'], 'public');
                if ($newLogoPath === false) {
                    throw new \RuntimeException('Failed to store logo file');
                }
                if ($this->clan->logo) {
                    Storage::disk('public')->delete($this->clan->logo);
                }

                $updateData['logo'] = $newLogoPath;
            }

            if ($this->image) {
                $newImagePath = $this->image->store('clans/'.$data['alias'], 'public');
                if ($newImagePath === false) {
                    throw new \RuntimeException('Failed to store image file');
                }
                if ($this->clan->image) {
                    Storage::disk('public')->delete($this->clan->image);
                }

                $updateData['image'] = $newImagePath;
            }

            if (
                $this->canSelectOwner()
                && ! empty($data['owner_user_id'])
                && (int) $data['owner_user_id'] !== $this->clan->owner_user_id
            ) {
                $updateData['owner_user_id'] = (int) $data['owner_user_id'];
            }

            $this->clan->update($updateData);
        });

        return redirect()->route('clans.show', ['clan' => $this->clan->slug]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = (new ClanUpdateRequest)->rulesFor($this->clan);

        if ($this->canSelectOwner()) {
            $rules['owner_user_id'] = [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
                Rule::notIn([$this->clan->owner_user_id]),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (empty($value)) {
                        return;
                    }

                    $user = User::query()->find($value);

                    if (! $user) {
                        return;
                    }

                    $error = $this->validateOwnerCandidate($user);
                    if ($error) {
                        $fail($error);
                    }
                },
            ];
        }

        return $rules;
    }

    public function canSelectOwner(): bool
    {
        return auth()->user()->hasRole(config('permission.super_admin_role', 'sudo'));
    }

    public function selectUser(?int $userId = null): void
    {
        if (is_null($userId)) {
            $this->reset('searchUser', 'selectedUserId', 'selectedUserName', 'owner_user_id');
            $this->resetValidation('owner_user_id');

            return;
        }

        if (! $this->canSelectOwner()) {
            return;
        }

        $user = User::query()->find($userId);

        if (! $user) {
            $this->reset('selectedUserId', 'selectedUserName', 'owner_user_id');

            return;
        }

        $error = $this->validateOwnerCandidate($user);
        if ($error) {
            $this->addError('owner_user_id', $error);
            $this->reset('selectedUserId', 'selectedUserName', 'owner_user_id');

            return;
        }

        $this->resetValidation('owner_user_id');
        $this->searchUser = '';
        $this->selectedUserId = $userId;
        $this->selectedUserName = $this->users()->get($userId, '');
        $this->owner_user_id = $userId;
    }

    private function validateOwnerCandidate(User $user): ?string
    {
        if ($user->ownedClan()->exists()) {
            return __('hll.clans.create.error_owner_already_has_clan');
        }

        if ($user->hasAnyRole(['clan_owner', 'clan_helper'])) {
            return __('hll.clans.create.error_owner_is_clan_owner_without_clan');
        }

        return null;
    }
};
