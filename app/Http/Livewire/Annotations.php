<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use App\Models\{Annotation, User};
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Annotations extends Component
{
    /**
     * Undocumented variable
     *
     * @var boolean
     */
    public bool $currentTeamIsEmpty = false;

    /**
     * Annotation form.
     *
     * @var boolean
     */
    public bool $displayAnnotationCreationForm = false;

    /**
     * User's accounts suggestions.
     *
     * @var boolean
     */
    public bool $displaySuggestions = false;

    /**
     * Assignees list.
     *
     * @var boolean
     */
    public bool $displayAssignees = false;

    /**
     * The given model
     *
     * @var \Illuminate\Database\Eloquent\Model $model
     */
    public Model $model;

    /**
     * Annotations collection
     *
     * @var \Illuminate\Database\Eloquent\Collection $annotations
     */
    public Collection $annotations;

    /**
     * Suggestion list.
     *
     * @var Collection
     */
    public Collection $suggestions;

    /**
     * Assignees.
     *
     * @var \Illuminate\Support\Collection
     */
    public \Illuminate\Support\Collection $assignees;

    /**
     * Search criteria.
     *
     * @var string
     */
    public string $query = '';

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'user_id' => '',
        'content' => '',
        'custom_properties' => [
            'title' => '',
            'assignees' => [],
            'status' => '',
            'priority' => '',
            'commissioning_at' => null,
            'feature' => [
                'values' => null,
                'geometry' => null,
            ],
        ],
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'state.content' => 'required|string',
        'state.custom_properties.title' => 'required|string',
        'state.custom_properties.assignees' => 'required|array',
        'state.custom_properties.status' => 'required|string',
        'state.custom_properties.priority' => 'required|string',
        'state.custom_properties.commissioning_at' => 'required|date',
    ];

    /**
     * Event Listeners.
     *
     * @var array
     */
    protected $listeners = [
        'featureAtPixel' => 'setFeatureAtPixel',
    ];

    /**
     * Set component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->state['user_id'] = Auth::id();

        // Get all of the team's users, excluding the owner...
        $this->suggestions = Auth::user()->currentTeam->users;

        $this->currentTeamIsEmpty = $this->suggestions->isEmpty();

        $this->assignees = collect([]);
    }

    /**
     * Feature at pixel.
     *
     * @param array $payload - feature json object.
     *
     * @return void
     */
    public function setFeatureAtPixel(array $payload = []): void
    {
        if (Arr::exists($payload, 'values_')) {
            $this->state['custom_properties']['feature']['values'] = Arr::except($payload['values_'], ['geometry']);
        }

        if (Arr::has($payload, 'values_.geometry')) {
            $this->state['custom_properties']['feature']['geometry'] = Arr::get($payload, 'values_.geometry');
        }
    }

    /**
     * Set default state.
     *
     * @return void
     */
    public function resetState(): void
    {
        $this->state = [
            'user_id' => Auth::id(),
            'content' => '',
            'custom_properties' => [
                'title' => '',
                'assignees' => [],
                'status' => '',
                'priority' => '',
                'commissioning_at' => null,
                'feature' => [
                    'values' => null,
                    'geometry' => null,
                ],
            ],
        ];

        $this->suggestions = Auth::user()->currentTeam->users;

        $this->assignees = collect([]);
    }

    /**
     * Add assigned to list.
     *
     * @param User $user
     * @return void
     */
    public function toggleAssigned(User $user): void
    {
        $this->assignees->push($user);
        $this->displayAssignees = true;

        // Update suggestions list state
        $this->suggestions = $this->suggestions
            ->except($this->assignees->pluck('id')->toArray());
    }

    /**
     * Remove assigned from list.
     *
     * @param User $user
     * @return void
     */
    public function removeAssigned(User $user): void
    {
        $this->assignees = $this->assignees->filter(
            fn($item) => $item['id'] !== $user->id
        );

        $this->suggestions->push($user);

        if ($this->assignees->count() === 0) {
            $this->displayAssignees = false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Annotation
     */
    public function store(): Annotation
    {
        $this->state['custom_properties']['assignees'] = $this->assignees->toArray();

        $this->resetErrorBag();

        $this->validate();

        $annotation = $this->model->annotation()->create($this->state);

        $this->resetState();

        $this->displayAnnotationCreationForm = false;

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Successfully saved annotation.')
        ]);

        return $annotation;
    }
}
