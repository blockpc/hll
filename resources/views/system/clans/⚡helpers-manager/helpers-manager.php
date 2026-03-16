<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Mail\NewUserCreatedMail;
use App\Models\Clan;
use App\Models\User;
use Blockpc\App\Rules\AreEqualsRule;
use Blockpc\Traits\AlertBrowserEvent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use AlertBrowserEvent;

    public Clan $clan;

    public string $name = '';

    public string $email = '';

    public int|string $editingHelperId = 0;

    public string $editingHelperName = '';

    public string $editingHelperEmail = '';

    public string $current_name = '';

    public function mount(): void
    {
        $this->authorizeOwner();
    }

    #[Computed]
    public function members(): Collection
    {
        return $this->clan->members;
    }

    public function save(): RedirectResponse|Redirector|null
    {
        $this->authorizeOwner();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:64'],
            'email' => ['required', 'string', 'email', 'max:64', 'unique:users,email'],
        ]);

        $createdUser = DB::transaction(function () use ($data) {
            $helper = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(12)),
            ]);
            $helper->assignRole('clan_helper');

            $this->clan->members()->attach($helper->id, [
                'membership_role' => ClanMembershipRoleEnum::Helper->value,
            ]);

            return $helper;
        });

        session()->flash('success', __('hll.clans.managers.create.message_success', ['name' => $createdUser->name]));

        try {
            Mail::to($createdUser->email)->send(new NewUserCreatedMail($createdUser));
        } catch (\Exception $e) {
            logger()->error('Failed to send new user created email', [
                'user_id' => $createdUser->id,
                'email_hash' => hash('sha256', mb_strtolower($createdUser->email)),
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', __('system.users.create.email_error_message'));
        }

        return redirect()->route('clans.show', ['clan' => $this->clan->slug]);
    }

    private function authorizeOwner(): void
    {
        abort_unless(
            auth()->user()?->can('manageHelpers', $this->clan),
            403,
            __('hll.clans.managers.create.403')
        );
    }

    public function cancel(): void
    {
        $this->cancelModal('create-helper-manager');
    }

    public function showEditModal(int|string $helperId): void
    {
        $this->authorizeOwner();

        $this->editingHelperId = $helperId;
        $helper = $this->ensureMemberExists($this->editingHelperId);
        $this->editingHelperName = $helper->name;
        $this->editingHelperEmail = $helper->email;

        $this->modal('edit-helper-manager')->show();
    }

    public function editHelper(): void
    {
        $this->authorizeOwner();

        $data = $this->validate([
            'editingHelperName' => ['required', 'string', 'max:64'],
            'editingHelperEmail' => ['required', 'string', 'email', 'max:64', "unique:users,email,{$this->editingHelperId}"],
        ], [], [
            'editingHelperName' => __('hll.clans.managers.edit.name'),
            'editingHelperEmail' => __('hll.clans.managers.edit.email'),
        ]);

        $helper = $this->ensureMemberExists($this->editingHelperId);
        $helper->update([
            'name' => $data['editingHelperName'],
            'email' => $data['editingHelperEmail'],
        ]);

        $message = __('hll.clans.managers.edit.message_success', ['name' => $helper->name]);

        $this->alert($message, 'success', __('hll.clans.managers.edit.title'));

        $this->cancelModal('edit-helper-manager');
    }

    public function cancelModalHelper(): void
    {
        $this->modal('edit-helper-manager')->close();
    }

    public function showDeleteModal(int|string $helperId): void
    {
        $this->authorizeOwner();

        $this->editingHelperId = $helperId;
        $helper = $this->ensureMemberExists($this->editingHelperId);
        $this->editingHelperName = $helper->name;
        $this->current_name = '';

        $this->modal('delete-helper-manager')->show();
    }

    public function deleteHelper(): void
    {
        $this->validate([
            'current_name' => ['required', 'string', (new AreEqualsRule($this->editingHelperName, __('hll.clans.managers.delete.current_name_error')))],
        ], [
            'current_name.in' => __('hll.clans.managers.delete.current_name_write', ['name' => $this->editingHelperName]),
        ]);

        $helper = $this->ensureMemberExists($this->editingHelperId);
        $this->clan->members()->detach($helper->id);

        $message = __('hll.clans.managers.delete.message_success', ['name' => $helper->name]);

        $this->alert($message, 'success', __('hll.clans.managers.delete.title'));

        $this->cancelDeleteModal();
    }

    public function cancelDeleteModal(): void
    {
        $this->cancelModal('delete-helper-manager');
    }

    public function cancelModal(string $modal): void
    {
        $this->resetExcept('clan');
        $this->clearValidation();
        $this->modal($modal)->close();
    }

    private function ensureMemberExists(int|string $helperId): User
    {
        $helper = User::findOrFail($helperId);

        if (! $this->clan->members()->where('user_id', $helper->id)->exists()) {
            abort(404);
        }

        return $helper;
    }
};
