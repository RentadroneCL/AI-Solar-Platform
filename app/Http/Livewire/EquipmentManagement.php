<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Models\{Site, EquipmentType, Equipment};

class EquipmentManagement extends Component
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
     * Search criteria.
     *
     * @var string
     */
    public string $query = '';

    /**
     * Equipments collection.
     *
     * @var Collection
     */
    public Collection $equipments;

    /**
     * Equipments types collection.
     *
     * @var integer|Collection
     */
    public int|Collection $equipmentTypes;

    /**
     * Indicates if equipment creation is being confirmed.
     *
     * @var bool
     */
    public bool $displayEquipmentCreationForm = false;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'name' => '',
        'brand' => '',
        'model' => '',
        'serial' => '',
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
        'selectedEquipmentType',
        'stored-equipment' => 'updateCollection',
    ];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->equipmentTypes = $this->site->equipmentTypes->count();

        $this->equipments = $this->site->equipments;

        $this->state['custom_properties'] = [
            ['key' => null, 'value' => null],
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(): void
    {
        $this->resetErrorBag();

        $this->validate();

        DB::transaction(function () {
            // Checking equipment quantity to proceed.
            $counter = $this->site->equipments()
                ->where(['equipment_type_id' => $this->equipmentType->id])
                ->count();

            if ($this->equipmentType->quantity === $counter) {
                // there's no way to proceed. equipment are completed.
                $this->emit('completed-equipment');
                return;
            }

            Equipment::create($this->state);

            $this->emit('stored-equipment');
        });

        $this->state['name'] = '';
        $this->state['brand'] = '';
        $this->state['model'] = '';
        $this->state['serial'] = '';
        $this->state['custom_properties'] = [
            ['key' => null, 'value' => null],
        ];

        $this->displayEquipmentCreationForm = false;

        $this->equipmentType = null;
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
     * Search drop-down data filtered.
     *
     * @return void
     */
    public function search(): void
    {
        $filtered = $this->site->equipmentTypes()
            ->select('name', 'id')
            ->where('name', 'like', '%' . $this->query . '%')
            ->get();

        $this->equipmentTypes = $filtered;
    }

    /**
     * Set equipment type.
     *
     * @param EquipmentType $equipmentType
     * @return void
     */
    public function selectedEquipmentType(EquipmentType $equipmentType): void
    {
        $this->equipmentType = $equipmentType;

        $this->state['equipment_type_id'] = $equipmentType->id;

        $this->query = '';
    }

    /**
     * Discard owner selection.
     *
     * @return void
     */
    public function discardSelection(): void
    {
        $this->equipmentType = null;

        $this->state['equipment_type_id'] = null;

        $this->emit('discard-selected');
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
    public function updatedDisplayEquipmentCreationForm(): void
    {
        $this->dispatchBrowserEvent('equipment-content-change');
    }
}
