<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\Redirector;
use App\Models\{User, Site};
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SiteManagement extends Component
{
    /**
     * Selected owner.
     *
     * @var User|null
     */
    public ?User $owner;

    /**
     * Search criteria.
     *
     * @var string
     */
    public string $query = '';

    /**
     * Indicates if site creation is being confirmed.
     *
     * @var bool
     */
    public bool $confirmingSiteCreation = false;

    /**
     * Users collection.
     *
     * @var Collection|null
     */
    public ?Collection $users;

    /**
     * Sites collection.
     *
     * @var Collection
     */
    public Collection $sites;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'user_id' => '',
        'name' => '',
        'address' => '',
        'latitude' => '',
        'longitude' => '',
        'commissioning_date' => ''
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'state.user_id' => 'required|integer',
        'state.name' => 'required|string|min:6',
        'state.address' => 'required|string|min:6',
        'state.latitude' => 'required|numeric',
        'state.longitude' => 'required|numeric',
        'state.commissioning_date' => 'required|date',
    ];

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = ['selectedOwner'];

    /**
     * Set component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->sites = Auth::user()->sites;

        if (Auth::user()->hasRole('administrator')) {
            $this->sites = Site::all();
        }
    }

    /**
     * Search drop-down data filtered.
     *
     * @return void
     */
    public function search(): void
    {
        $filtered = User::query()->select('email', 'name', 'id', 'profile_photo_path')
            ->where(function (Builder $query) {
                $query->where('name', 'like', '%' . $this->query . '%')
                    ->orWhere('email', 'like', '%' . $this->query . '%');
            })
            ->get();

        $this->users = $filtered;
    }

    /**
     * Set site owner property.
     *
     * @param User $user
     * @return void
     */
    public function selectedOwner(User $user): void
    {
        $this->owner = $user;

        $this->state['user_id'] = $user->id;

        $this->users = null;

        $this->query = '';

        $this->emit('selected');
    }

    /**
     * Discard owner selection.
     *
     * @return void
     */
    public function discardSelection(): void
    {
        $this->owner = null;

        $this->state['user_id'] = null;

        $this->emit('discard-selected');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Redirector
     */
    public function store(): Redirector
    {
        $this->resetErrorBag();

        $this->validate();

        $site = Site::create($this->state);

        return redirect()->route('site.show', $site);
    }
}
