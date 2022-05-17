<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use App\Models\{Site, EquipmentType};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\{Auth, Hash, Validator};

class EquipmentTypeTable extends Component
{
    /**
     * Site model.
     *
     * @var Site
     */
    public Site $site;

    /**
     * Equipment type model.
     *
     * @var EquipmentType|null
     */
    public ?EquipmentType $equipmentType = null;

    /**
     * Equipments types collection.
     *
     * @var Collection
     */
    public Collection $equipmentsTypes;

    /**
     * Indicates if equipment type deletion is being confirmed.
     *
     * @var bool
     */
    public bool $confirmingEquipmentTypeDeletion = false;

    /**
     * Indicates if equipment type edition is being confirmed.
     *
     * @var boolean
     */
    public bool $confirmingEquipmentTypeEdition = false;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'name' => '',
        'custom_properties' => null,
    ];

    /**
     * The user's current password.
     *
     * @var string
     */
    public string $password = '';

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'password' => 'required|string|min:2'
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
        'editEquipmentType',
        'deleteEquipmentType',
        'stored-equipment-type' => 'updateCollection',
        'updated-equipment-type' => 'updateCollection',
        'deleted-equipment-type' => 'updateCollection',
    ];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->state['custom_properties'] = [
            ['key' => null, 'value' => null],
        ];
    }

    public function update(): void
    {
        abort_unless(Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        Validator::make($this->state, [
            'state.name' => 'required|string|min:2',
            'state.custom_properties.*.key' =>'nullable|string|min:2',
            'state.custom_properties.*.value' =>'nullable|string|min:2',
        ]);

        $this->equipmentType->update($this->state);
        $this->equipmentType = null;

        $this->confirmingEquipmentTypeEdition = false;

        $this->emit('updated-equipment-type');
    }

    /**
     * Delete the current equipment type.
     *
     * @return void
     */
    public function destroy(): void
    {
        abort_unless(Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        Validator::make(['password' => $this->password], ['password' => 'required|string|min:2']);

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $this->equipmentType->delete();
        $this->equipmentType = null;

        $this->password = '';

        $this->confirmingEquipmentTypeDeletion = false;

        $this->emit('deleted-equipment-type');
    }

    /**
     * Confirm that the user would like to edit the equipment type.
     *
     * @param EquipmentType $equipmentType
     * @return void
     */
    public function editEquipmentType(EquipmentType $equipmentType): void
    {
        abort_unless(Auth::user()->hasRole('administrator'), 403);

        $this->equipmentType = $equipmentType;

        $this->state['name'] = $equipmentType->name;
        $this->state['custom_properties'] = $equipmentType->custom_properties;

        $this->confirmingEquipmentTypeEdition = true;
    }

    /**
     * Confirm that the user would like to delete the equipment type.
     *
     * @param EquipmentType $equipmentType
     * @return void
     */
    public function deleteEquipmentType(EquipmentType $equipmentType): void
    {
        abort_unless(Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        $this->password = '';

        $this->confirmingEquipmentTypeDeletion = true;

        $this->equipmentType = $equipmentType;

        $this->dispatchBrowserEvent('confirming-delete-equipment-type');
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
     * Update equipments types collection.
     *
     * @return void
     */
    public function updateCollection(): void
    {
        $this->equipmentsTypes = EquipmentType::all();

        $this->dispatchBrowserEvent('equipment-type-content-change');
    }
}
