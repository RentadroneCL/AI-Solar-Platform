<?php

namespace App\Http\Livewire;

use App\Models\Site;
use Livewire\Component;
use Illuminate\Support\Arr;
use Livewire\WithFileUploads;
use App\Models\EquipmentType;
use Illuminate\Database\Eloquent\Collection;

class EquipmentTypeManagement extends Component
{
    use WithFileUploads;

    /**
     * Site model.
     *
     * @var Site
     */
    public Site $site;

    /**
     * Indicates if equipment type creation is being confirmed.
     *
     * @var bool
     */
    public bool $displayEquipmentTypeCreationForm = false;

    /**
     * Display import CSV modal.
     *
     * @var boolean
     */
    public bool $showImportModal = false;

    /**
     * Equipments types collection.
     *
     * @var Collection
     */
    public Collection $equipmentsTypes;

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
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'state.name' => 'required|string|min:2',
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
        'stored-equipment-type' => 'updateCollection',
    ];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->equipmentsTypes = EquipmentType::all();

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

        EquipmentType::create($this->state);

        $this->state['name'] = '';
        $this->state['custom_properties'] = [
            ['key' => null, 'value' => null],
        ];

        $this->displayEquipmentTypeCreationForm = false;

        $this->emit('stored-equipment-type');
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
