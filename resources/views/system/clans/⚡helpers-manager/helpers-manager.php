<?php

use App\Enums\ClanMembershipRoleEnum;
use App\Mail\NewUserCreatedMail;
use App\Models\Clan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public Clan $clan;

    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->authorizeOwner();
    }

    public function save(): RedirectResponse|Redirector|null
    {
        $this->authorizeOwner();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $createdUser = DB::transaction(function () use ($data) {
            $helper = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(12)),
            ]);

            $this->clan->members()->attach($helper->id, [
                'membership_role' => ClanMembershipRoleEnum::Helper->value,
            ]);

            return $helper;
        });

        session()->flash('success', __('hll.clans.managers.create.message_success', ['name' => $createdUser->name]));

        try {
            Mail::to($this->email)->send(new NewUserCreatedMail($createdUser));
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
            auth()->user()?->hasRole('clan_owner') &&
            $this->clan->owner_user_id === auth()->id(),
            403,
            __('hll.clans.managers.create.403')
        );
    }
};
