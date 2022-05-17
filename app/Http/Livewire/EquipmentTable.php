<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use App\Models\{Site, Equipment};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\{Auth, Validator, Hash};

class EquipmentTable extends Component
{
    /**
     * Indicates if equipment edition is being confirmed.
     *
     * @var boolean
     */
    public bool $confirmingEquipmentEdition = false;

    /**
     * Indicates if equipment deletion is being confirmed.
     *
     * @var boolean
     */
    public bool $confirmingEquipmentDeletion = false;

    /**
     * Check ownership.
     *
     * @var boolean
     */
    protected bool $isOwner = false;

    /**
     * The user's current password.
     *
     * @var string
     */
    public string $password = '';

    /**
     * Site model.
     *
     * @var Site
     */
    public Site $site;

    /**
     * Equipment model.
     *
     * @var Equipment|null
     */
    public ?Equipment $equipment;

    /**
     * Equipments collection.
     *
     * @var Collection
     */
    public Collection $equipments;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'name' => '',
        'brand' => null,
        'model' => null,
        'serial' => null,
        'custom_properties' => null,
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'state.name' => 'required|string|min:2',
        'state.brand' => 'nullable|string|min:2',
        'state.model' => 'nullable|string|min:2',
        'state.serial' => 'nullable|string|min:2',
        'state.custom_properties.*.key' =>'nullable|string|min:2',
        'state.custom_properties.*.value' =>'nullable|string|min:2',
        //'file' => 'required|file|mimes:csv',
    ];

    /**
     * Custom error messages.
     *
     * @var array
     */
    protected $messages = [
        'state.name.required' => 'The name field is required.',
        'state.custom_properties.*.key.filled' => 'The feature field must have a value.',
        'state.custom_properties.*.value.filled' => 'The value field must have a value.',
    ];

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = [
        'editEquipment',
        'deleteEquipment',
        'stored-equipment' => 'updateCollection',
        'updated-equipment' => 'updateCollection',
        'deleted-equipment' => 'updateCollection',
    ];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->isOwner = $this->site->isOwner();

        $this->equipments = $this->site->equipments;

        $this->state['custom_properties'] = [
            ['key' => null, 'value' => null],
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @return void
     */
    public function update(): void
    {
        abort_unless(!$this->isOwner || !Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        $this->validate();

        $this->equipment->update($this->state);
        $this->equipment = null;

        $this->confirmingEquipmentEdition = false;

        $this->emit('updated-equipment');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return void
     */
    public function destroy(): void
    {
        abort_unless(!$this->isOwner || !Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        Validator::make(['password' => $this->password], ['password' => 'required|string|min:2']);

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $this->equipment->delete();
        $this->equipment = null;

        $this->password = '';

        $this->confirmingEquipmentDeletion = false;

        $this->emit('deleted-equipment');
    }

    /**
     * Confirm that the user would like to edit the equipment.
     *
     * @param Equipment $equipment
     * @return void
     */
    public function editEquipment(Equipment $equipment): void
    {
        abort_unless(!$this->isOwner || !Auth::user()->hasRole('administrator'), 403);

        $this->equipment = $equipment;

        $this->state['name'] = $equipment->name;
        $this->state['brand'] = $equipment->brand;
        $this->state['model'] = $equipment->model;
        $this->state['serial'] = $equipment->serial;
        $this->state['custom_properties'] = $equipment->custom_properties;

        $this->confirmingEquipmentEdition = true;

        $this->dispatchBrowserEvent('equipment-content-change');
    }

    /**
     * Confirm that the user would like to delete the equipment.
     *
     * @param Equipment $equipment
     * @return void
     */
    public function deleteEquipment(Equipment $equipment): void
    {
        abort_unless(!$this->isOwner || !Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        $this->password = '';

        $this->equipment = $equipment;

        $this->confirmingEquipmentDeletion = true;

        $this->dispatchBrowserEvent('confirming-delete-equipment');
    }

    /**
     * Create dynamic input fields.
     *
     * @return void
     */
    public function addCustomPropertyInput(): void
    {
        array_push($this->state['custom_properties'], ['key' => null, 'value' => null]);
    }

    /**
     * Remove specific input field.
     *
     * @param integer|null $key
     * @return void
     */
    public function removeCustomPropertyInput(int $key = null): void
    {
        Arr::forget($this->state['custom_properties'], $key);

        $this->emit('removed-custom-property');
    }

    /**
     * Update equipments collection.
     *
     * @return void
     */
    public function updateCollection(): void
    {
        $this->equipments = $this->site->equipments;

        $this->dispatchBrowserEvent('equipment-content-change');
    }

    /**
     * Runs after the property is updated.
     *
     * @return void
     */
    public function updatedConfirmingEquipmentEdition(): void
    {
        $this->dispatchBrowserEvent('equipment-content-change');
    }

    /**
     * Runs after the property is updated.
     *
     * @return void
     */
    public function updatedConfirmingEquipmentDeletion(): void
    {
        $this->dispatchBrowserEvent('equipment-content-change');
    }
}
