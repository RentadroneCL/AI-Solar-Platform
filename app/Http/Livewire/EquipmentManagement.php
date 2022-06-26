<?php

namespace App\Http\Livewire;

use League\Csv\Writer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use App\Models\{Equipment, EquipmentType, Site};
use Illuminate\Support\{Arr, LazyCollection, Str};
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\{Auth, DB, Hash, Validator};

class EquipmentManagement extends Component
{
    use WithFileUploads;

    /**
     * Search criteria.
     *
     * @var string
     */
    public string $query = '';

    /**
     * The user's current password.
     *
     * @var string
     */
    public string $password = '';

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
     * Display import CSV modal.
     *
     * @var boolean
     */
    public bool $showImportModal = false;

    /**
     * CSV file.
     */
    public $file;

    /**
     * Uploaded filename.
     *
     * @var string
     */
    public string $filename = 'Browse file';

    /**
     * CSV headers.
     *
     * @var array
     */
    protected array $csvHeader = [];

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
        'custom_properties' => [
            ['key' => null, 'value' => null],
        ],
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
        'edit-row' => 'edit',
        'delete-row' => 'delete',
        'selectedEquipmentType',
        'saved-type-row' => 'updateEquipmentTypes',
    ];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->equipmentTypes = EquipmentType::query()
            ->leftJoin('sites_information', 'sites_information.id', '=', 'equipment_type.site_id')
            ->where(['sites_information.id' => $this->site->id])
            ->count();

        $this->equipments = Equipment::query()
            ->leftJoin('equipment_type', 'equipment_type.id', '=', 'equipments.equipment_type_id')
            ->select('equipments.id', 'equipment_type.name as type', 'equipments.name', 'brand', 'model', 'serial', 'equipments.custom_properties')
            ->where(['equipment_type.site_id' => $this->site->id])
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        $this->validate();

        DB::transaction(function () {
            // Checking equipment quantity to proceed.
            $counter = Equipment::query()
                ->leftJoin('equipment_type', 'equipment_type.id', '=', 'equipments.equipment_type_id')
                ->leftJoin('sites_information', 'sites_information.id', '=', 'equipment_type.site_id')
                ->where(['equipments.equipment_type_id' => $this->equipmentType->id])
                ->where(['equipment_type.site_id' => $this->site->id])
                ->count();

            // Checking to proceed. if equipment types registration isn't completed.
            if ($this->equipmentType->quantity === $counter) {
                $this->emit('completed-equipment-quantity');
                return;
            }

            $this->state['custom_properties']
                = collect($this->state['custom_properties'])
                ->reject(fn($item) => empty($item['key']) && empty($value))
                ->toArray();

            $equipment = Equipment::create(
                Arr::except(array: $this->state, keys: 'custom_properties')
            );

            // Adds a new feature or updates an existing one.
            $equipment->setCustomProperty(
                name: 'features',
                value: $this->state['custom_properties']
            );
            $equipment->save();
        });

        $this->resetState();

        $this->displayEquipmentCreationForm = false;

        $this->equipmentType = null;

        $this->equipments = Equipment::query()
            ->leftJoin('equipment_type', 'equipment_type.id', '=', 'equipments.equipment_type_id')
            ->select('equipments.id', 'equipment_type.name as type', 'equipments.name', 'brand', 'model', 'serial', 'equipments.custom_properties')
            ->where(['equipment_type.site_id' => $this->site->id])
            ->get();

        $this->emit('saved-row');
    }

    /**
     * Confirm that the user would like to edit the equipment.
     *
     * @param array $row
     *
     * @return void
     */
    public function edit(array $row): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->state['id'] = Arr::get($row, 'id');
        $this->state['name'] = Arr::get($row, 'name');
        $this->state['brand'] = Arr::get($row, 'brand');
        $this->state['model'] = Arr::get($row, 'model');
        $this->state['serial'] = Arr::get($row, 'serial');
        $this->state['custom_properties'] = Arr::has($row, 'custom_properties.features')
            ? Arr::get($row, 'custom_properties.features')
            : [['key' => '', 'value' => '']];

        $this->confirmingEquipmentEdition = true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @return void
     */
    public function update(): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        $this->validate();

        // Remove empty values from the custom properties array.
        $this->state['custom_properties']
            = collect($this->state['custom_properties'])
            ->reject(fn($item) => empty($item['key']) && empty($value))
            ->toArray();

        try {
            DB::transaction(function () {
                $equipment = Equipment::findOrFail(Arr::get($this->state, 'id'));
                $equipment->update(Arr::except(array: $this->state, keys: 'custom_properties'));

                // Adds a new feature or updates an existing one.
                $equipment->setCustomProperty(
                    name: 'features',
                    value: $this->state['custom_properties']
                );
                $equipment->save();
            });

            Arr::forget($this->state, 'id');

            $this->resetState();

            $this->confirmingEquipmentEdition = false;

            $this->emit('edited-row');

        } catch (\Throwable $th) {

            $this->emit('error', $th->getMessage());

            $this->confirmingEquipmentEdition = false;
        }
    }

    /**
     * Confirm that the user would like to delete the equipment.
     *
     * @param array $row
     *
     * @return void
     */
    public function delete(array $row): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->state['id'] = Arr::get($row, 'id');

        $this->confirmingEquipmentDeletion = true;
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return void
     */
    public function destroy(): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        Validator::make(['password' => $this->password], ['password' => 'required|string|min:2']);

        if (!Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        Equipment::findOrFail(Arr::get($this->state, 'id'))->delete();

        $this->resetState();

        $this->password = '';

        $this->confirmingEquipmentDeletion = false;

        $this->emit('deleted-row');
    }

    /**
     * Import CSV file.
     *
     * @return void
     */
    public function import(): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        Validator::make(['file' => $this->file], [
            'file' => 'required|file|mimes:csv',
        ]);

        $collection = collect([]);

        // Read CSV file.
        LazyCollection::make(function () {
            $file = fopen($this->file->temporaryUrl(), 'r');

            while ($data = fgetcsv($file)) {
                yield $data;
            }
        })->each(fn($item) => $collection->push($item));

        $this->csvHeader = $collection->first();

        $filtered
            = $collection
            ->skip(1)
            ->map(fn($item) => array_combine($this->csvHeader, $item))
            ->map(function ($item) {
                return [
                    'equipment_type_id' => $item['equipment_type_id'],
                    'uuid' => $item['uuid'] ?? (string) Str::uuid(),
                    'name' => $item['name'],
                    'brand' => $item['brand'] ?? null,
                    'model' => $item['model'] ?? null,
                    'serial' => $item['serial'] ?? null,
                    'custom_properties' => collect([])->toJson(),
                ];
            })
            ->toArray();

        try {
            DB::transaction(fn() => Equipment::insert($filtered));

            $this->showImportModal = false;

            $this->emit('stored-equipment');

            $this->dispatchBrowserEvent('saved');
        } catch (\Throwable $th) {
            $this->showImportModal = false;

            $this->dispatchBrowserEvent('error');
        }
    }

    /**
     * Export CSV data.
     *
     * @return void
     */
    public function export(): StreamedResponse
    {
        $collection = $this->site->equipments()->get(
            columns: [
                'uuid',
                'equipments.name',
                'brand',
                'model',
                'serial',
                'equipments.created_at',
                'equipments.updated_at'
            ]
        );

        $csv = Writer::createFromFileObject(file: new \SplTempFileObject());
        $csv->insertOne(
            record: collect($collection->first()->toArray())
                ->except('laravel_through_key')
                ->keys()
                ->toArray()
        );
        $csv->insertAll(
            records: collect($collection->toArray())
                ->map(fn($item) => Arr::except($item, 'laravel_through_key'))
                ->toArray()
        );

        return response()->streamDownload(
            callback: fn() => $csv->output(),
            name: 'equipments-data.csv'
        );
    }

    /**
     * Reset component state.
     *
     * @return void
     */
    public function resetState(): void
    {
        if (Arr::has($this->state, 'id')) {
            Arr::forget($this->state, 'id');
        }

        $this->state['name'] = '';
        $this->state['brand'] = '';
        $this->state['model'] = '';
        $this->state['serial'] = '';
        $this->state['custom_properties'] = [
            ['key' => '', 'value' => ''],
        ];
    }

    /**
     * Create dynamic input fields.
     *
     * @return void
     */
    public function addCustomPropertyInput(): void
    {
        array_push(
            $this->state['custom_properties'],
            ['key' => '', 'value' => '']
        );
    }

    /**
     * Remove specific input field.
     *
     * @param integer|null $key
     * @return void
     */
    public function removeCustomPropertyInput(int $key = null): void
    {
        Arr::forget(
            array: $this->state['custom_properties'],
            keys: $key
        );

        $this->emit('removed-custom-property');
    }

    /**
     * Search drop-down data filtered.
     *
     * @return void
     */
    public function search(): void
    {
        $this->equipmentTypes = EquipmentType::query()
            ->select('equipment_type.name', 'equipment_type.id')
            ->leftJoin('sites_information', 'sites_information.id', '=', 'equipment_type.site_id')
            ->where(['sites_information.id' => $this->site->id])
            ->where('equipment_type.name', 'like', '%' . $this->query . '%')
            ->get();
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
        $this->equipments = $this->site->equipments()->get();

        $this->dispatchBrowserEvent('equipment-content-change');
    }

    /**
     * Equipment type aggregate value.
     *
     * @return void
     */
    public function updateEquipmentTypes(): void
    {
        $this->equipmentTypes = EquipmentType::query()
            ->leftJoin('sites_information', 'sites_information.id', '=', 'equipment_type.site_id')
            ->where(['sites_information.id' => $this->site->id])
            ->get();
    }

    /**
     * Updated file property.
     *
     * @return void
     */
    public function updatedFile(): void
    {
        $this->filename = $this->file->temporaryUrl();
    }

    /**
     * Update displayEquipmentCreationForm property.
     *
     * @return void
     */
    public function updatedDisplayEquipmentCreationForm(): void
    {
        $this->resetState();
    }
}
