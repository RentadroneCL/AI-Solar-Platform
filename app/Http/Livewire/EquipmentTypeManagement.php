<?php

namespace App\Http\Livewire;

use League\Csv\Writer;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\{Site, EquipmentType};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\{Arr, LazyCollection};
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\{Validator, DB, Auth, Hash};

class EquipmentTypeManagement extends Component
{
    use WithFileUploads;

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
     * The user's current password.
     *
     * @var string
     */
    public string $password = '';

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
    public Collection $equipmentTypes;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'name' => '',
        'quantity' => 0,
        'custom_properties' => null,
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'state.name' => 'required|string|min:2',
        'state.quantity' => 'required|integer|min:1',
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
        'state.quantity.required' => 'The quantity field is required.',
        'state.quantity.min' => 'The quantity must be at least 1.',
        'state.custom_properties.*.key.filled' => 'The feature field must have a value.',
        'state.custom_properties.*.value.filled' => 'The value field must have a value.',
    ];

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = [
        'edit-type' => 'edit',
        'delete-type' => 'delete',
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
            ->select('equipment_type.name', 'equipment_type.id', 'quantity', 'custom_properties')
            ->where(['sites_information.id' => $this->site->id])
            ->get();

        $this->state['site_id'] = $this->site->id;
        $this->state['custom_properties'] = [
            ['key' => '', 'value' => ''],
        ];
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

        $this->state['custom_properties']
            = collect($this->state['custom_properties'])
            ->reject(fn($item) => empty($item['key']) && empty($value))
            ->toArray();

        DB::transaction(function () {
            $type = EquipmentType::create(
                Arr::except(array: $this->state, keys: 'custom_properties')
            );

            // Adds a new feature or updates an existing one.
            $type->setCustomProperty(
                name: 'features',
                value: $this->state['custom_properties']
            );
            $type->save();
        });

        $this->resetState();

        $this->equipmentTypes = EquipmentType::query()
            ->leftJoin('sites_information', 'sites_information.id', '=', 'equipment_type.site_id')
            ->select('equipment_type.name', 'equipment_type.id', 'quantity', 'custom_properties')
            ->where(['sites_information.id' => $this->site->id])
            ->get();

        $this->displayEquipmentTypeCreationForm = false;

        $this->emit('saved-type-row');
    }

    /**
     * Confirm that the user would like to edit the equipment type.
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
        $this->state['quantity'] = Arr::get($row, 'quantity');
        $this->state['custom_properties'] = Arr::has($row, 'custom_properties.features')
            ? Arr::get($row, 'custom_properties.features')
            : [['key' => '', 'value' => '']];

        $this->confirmingEquipmentTypeEdition = true;
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
            ->reject(fn($item) => empty($item['key']) && empty($item['value']))
            ->toArray();

        try {
            DB::transaction(function () {
                $type = EquipmentType::findOrFail(Arr::get($this->state, 'id'));
                $type->update(Arr::except(array: $this->state, keys: 'custom_properties'));

                // Adds a new feature or updates an existing one.
                $type->setCustomProperty(
                    name: 'features',
                    value: $this->state['custom_properties']
                );
                $type->save();
            });

            $this->resetState();

            $this->emit('edited-type-row');

            $this->confirmingEquipmentTypeEdition = false;

        } catch (\Throwable $th) {

            $this->emit('error', $th->getMessage());

            $this->confirmingEquipmentTypeEdition = false;
        }
    }

    /**
     * Confirm that the user would like to delete the equipment type.
     *
     * @param array $row
     *
     * @return void
     */
    public function delete(array $row): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->state['id'] = Arr::get($row, 'id');

        $this->confirmingEquipmentTypeDeletion = true;
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

        EquipmentType::findOrFail(Arr::get($this->state, 'id'))->delete();

        $this->resetState();

        $this->password = '';

        $this->confirmingEquipmentTypeDeletion = false;

        $this->emit('deleted-type-row');
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
            ->map(fn($item) => $item = Arr::add($item, 'site_id', $this->site->id))
            ->map(fn($item) => $item = Arr::add($item, 'custom_properties', collect([])->toJson()))
            ->toArray();

        try {
            DB::transaction(fn() => EquipmentType::insert($filtered));

            $this->showImportModal = false;

            $this->emit('stored-equipment-type');

        } catch (\Throwable $th) {
            $this->showImportModal = false;

            $this->emit('error');
        }
    }

    /**
     * Export CSV data.
     *
     * @return void
     */
    public function export(): StreamedResponse
    {
        $collection = $this->site->equipmentTypes()->get(
            ['name', 'quantity', 'created_at', 'updated_at']
        );

        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['name', 'quantity', 'created_at', 'updated_at']);
        $csv->insertAll($collection->toArray());

        return response()->streamDownload(
            fn() => $csv->output(),
            'equipment-types-data.csv'
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
        $this->state['quantity'] = 0;
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
        Arr::forget($this->state['custom_properties'], $key);

        $this->emit('removed-custom-property');
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
     * Updated displayEquipmentTypeCreationForm property.
     *
     * @return void
     */
    public function updatedDisplayEquipmentTypeCreationForm(): void
    {
        $this->resetState();
    }
}
