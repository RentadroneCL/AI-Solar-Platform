<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Redirector;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class UserManagement extends Component
{
    /**
     * Indicates if user creation is being confirmed.
     *
     * @var bool
     */
    public bool $confirmingUserCreation = false;

    /**
     * Indicates if the password was randomly generated.
     *
     * @var boolean
     */
    public bool $confirmingRandomPassword = false;

    /**
     * Users collection.
     *
     * @var Collection
     */
    public Collection $users;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'state.name' => 'required|string|min:6',
        'state.email' => 'required|email',
        'state.password' => 'required|string|min:6|confirmed:password_confirmation',
        'state.password_confirmation' => 'required|string|min:6',
    ];

    /**
     * Store a newly created resource in storage.
     *
     * @return Redirector
     */
    public function store(): Redirector
    {
        $this->resetErrorBag();

        $this->validate();

        $this->state['password'] = Hash::make($this->state['password']);

        $user = User::create($this->state);

        // Create user's defaults team.
        $slice = Str::before($user->name, ' ');
        $teamName = $slice .'\'s'. ' Team';

        $user->ownedTeams()->create([
            'name' => $teamName,
            'personal_team' => true,
        ]);

        return redirect()->route('user.edit', $user);
    }

    /**
     * Generate random password.
     *
     * @return void
     */
    public function randomPassword(): void
    {
        $password = Str::random(8);

        $this->state['password'] = $password;

        $this->state['password_confirmation'] = $password;

        $this->confirmingRandomPassword = true;
    }
}
