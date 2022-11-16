<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use App\Models\{Inspection, Annotation, User};
use Illuminate\Support\Facades\{Auth, Hash, Validator};

class InspectionAnnotations extends Component
{
    /**
     * Search criteria.
     *
     * @var string
     */
    public string $query = '';

    /**
     * Suggestions list.
     *
     * @var Collection
     */
    public ?Collection $suggestions = null;

    /**
     * Indicates if annotation deletion is being confirmed.
     *
     * @var bool
     */
    public bool $confirmingAnnotationDeletion = false;

    /**
     * Suggestions list.
     *
     * @var boolean
     */
    public bool $displaySuggestions = false;

    /**
     * Overlay pane.
     *
     * @var boolean
     */
    public bool $displayOverlayPane = false;

    /**
     * The user's current password.
     *
     * @var string
     */
    public string $password = '';

    /**
     * Inspection model.
     *
     * @var Inspection
     */
    public Inspection $inspection;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'user_id' => null,
        'annotation_id' => null,
        'content' => '',
        'custom_properties' => [
            'title' => '',
            'assignees' => [],
            'status' => '',
            'priority' => '',
            'commissioning_at' => null,
        ],
    ];

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = [
        'edit-annotation' => 'edit',
        'delete-annotation' => 'delete',
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
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->state['user_id'] = Auth::id();

        // Get all of the team's users, excluding the owner...
        $this->suggestions = Auth::user()->currentTeam->users ?? collect([]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param array $payload - Current row selection.
     * @return void
     */
    public function edit(array $payload = []): void
    {
        $this->state = [
            'annotation_id' => $payload['id'],
            'content' => $payload['content'],
            'custom_properties' => [...$payload['custom_properties']],
        ];

        $this->displayOverlayPane = true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @return void
     */
    public function update(): void
    {
        try {
            abort_unless($this->inspection->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

            $this->resetErrorBag();
            $this->validate();

            $annotation = Annotation::findOrFail($this->state['annotation_id']);
            $annotation->update(
                Arr::except(array: $this->state, keys: ['annotation_id'])
            );

            $this->resetState();

            $this->emit('updated-annotation-row');

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Successfully updated annotation.')
            ]);
        } catch (\Throwable $th) {
            $this->resetState();
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('You don\'t have permission to perform this operation.')
            ]);
        }
    }

    /**
     * Handle delete event.
     *
     * @param array $payload
     * @return void
     */
    public function delete(array $payload = []): void
    {
        $this->state['annotation_id'] = Arr::get($payload, 'id');
        $this->confirmingAnnotationDeletion = true;
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return void
     */
    public function destroy(): void
    {
        try {
            abort_unless($this->inspection->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

            $this->resetErrorBag();

            Validator::make(['password' => $this->password], ['password' => 'required|string|min:2']);

            if (!Hash::check($this->password, Auth::user()->password)) {
                throw ValidationException::withMessages([
                    'password' => [__('This password does not match our records.')],
                ]);
            }

            Annotation::findOrFail($this->state['annotation_id'])->delete();

            $this->resetState();

            $this->emit('deleted-annotation-row');

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Successfully deleted annotation.')
            ]);
        } catch (\Throwable $th) {
            $this->resetState();
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('You don\'t have permission to perform this operation.')
            ]);
        }
    }

    /**
     * Reset component state.
     *
     * @return void
     */
    public function resetState(): void
    {
        $this->password = '';
        $this->confirmingAnnotationDeletion = false;
        $this->displayOverlayPane = false;

        // Get all of the team's users, excluding the owner...
        $this->suggestions = Auth::user()->currentTeam->users ?? collect([]);

        $this->state = [
            'user_id' => Auth::id(),
            'annotation_id' => null,
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
    }

    /**
     * Push the selected assigned to the current state.
     *
     * @param User $user
     * @return void
     */
    public function addAssigned(User $user): void
    {
        abort_unless($this->inspection->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $collection = collect($this->state['custom_properties']['assignees']);

        if ($collection->contains(fn($item) => $item['id'] === $user->id)) {
            return;
        }

        $this->state['custom_properties']['assignees'] = $collection->push($user);
    }

    /**
     * Remove the selected assigned from the current state.
     *
     * @param User $user
     * @return void
     */
    public function removeAssigned(User $user): void
    {
        abort_unless($this->inspection->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->state['custom_properties']['assignees']
            = collect($this->state['custom_properties']['assignees'])
            ->filter(fn($item) => $item['id'] !== $user->id)
            ->toArray();
    }
}
