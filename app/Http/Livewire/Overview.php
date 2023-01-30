<?php

namespace App\Http\Livewire;

use League\Csv\Writer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\{Auth, DB, Validator};
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\{LazyCollection, Arr, Collection, Carbon, Str};
use App\Models\{Site, Inspection, Equipment, EquipmentType, Annotation};

class Overview extends Component
{
    use WithFileUploads;

    /**
     * Equipment types expression.
     *
     * @var boolean
     */
    public $equipmentTypesIsEmpty = false;

    /**
     * Dataset expression.
     *
     * @var boolean
     */
    public $datasetIsEmpty = false;

    /**
     * Display equipment type creation form.
     *
     * @var boolean
     */
    public $displayEquipmentTypeCreationForm = false;

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
     * Site model.
     *
     * @var Site
     */
    public Site $site;

    /**
     * Inspection model.
     *
     * @var Inspection
     */
    public Inspection $inspection;

    /**
     * Equipment types collection.
     *
     * @var Collection
     */
    public Collection $equipmentTypes;

    /**
     * Custom property data.
     *
     * @var Collection
     */
    public Collection $dataset;

    /**
     * The component's state.
     *
     * @var array
     */
    public array $state = [
        'name' => 'Panel',
        'quantity' => 0,
        'custom_properties' => [
            ['key' => '', 'value' => ''],
        ],
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
        'state.equipment_type_id.required' => 'The equipment type field is required.',
    ];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->site = $this->inspection->site;

        $this->state['site_id'] = $this->site->id;

        $this->equipmentTypesIsEmpty = $this->site->equipmentTypes->isEmpty();

        $this->equipmentTypes = $this->site->equipmentTypes;

        $this->dataset = collect($this->inspection->getCustomProperty('data'));

        $this->datasetIsEmpty = $this->dataset->isEmpty();
    }

    /**
     * Parse CSV file.
     *
     * @return Collection
     */
    public function parseFile(): Collection
    {
        $collection = collect([]);

        LazyCollection::make(function () {
            $file = fopen($this->file->temporaryUrl(), 'r');

            while ($data = fgetcsv($file)) {
                yield $data;
            }
        })->each(fn($item) => $collection->push($item));

        return $collection->skip(1)->map(
            fn($item) => array_combine($collection->first(), $item)
        );
    }

    /**
     * Import CSV file.
     *
     * @return mixed
     */
    public function import(): mixed
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->resetErrorBag();

        $this->validate([
            'file' => 'required|file|mimes:csv',
            'state.equipment_type_id' => 'required|integer',
        ]);

        $csvData = $this->parseFile();

        $quantity = EquipmentType::findOrFail(
            $this->state['equipment_type_id']
        )->quantity;

        $counter = $this->site->equipments()
            ->where(['equipment_type_id' => $this->state['equipment_type_id']])
            ->count();

        try {
            DB::transaction(function () use ($csvData) {
                // Update the inspection metadata.
                $this->inspection->setCustomProperty('data', $csvData)->save();

                // Update inspection tracking data for the current item.
                $csvData->map(function ($item) {
                    $this->setEquipment($item);
                    $this->setAnnotation($item);
                });
            });

            return redirect()->route('inspection.show', $this->inspection);

        } catch (\Throwable $th) {
            $this->showImportModal = false;
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('An unexpected error has occurred')
            ]);
        }
    }

    /**
     * Export csv data.
     *
     * @return StreamedResponse
     */
    public function export(): StreamedResponse
    {
        $collection = $this->inspection->getCustomProperty('data');

        $header = Arr::except(collect($collection)->first(), 'equipment_type_id');

        $csv = Writer::createFromFileObject(file: new \SplTempFileObject());
        $csv->insertOne(
            record: collect($header)->keys()->toArray()
        );
        $csv->insertAll(
            records: collect($collection)
                ->map(fn($item) => Arr::except($item, 'equipment_type_id'))
                ->toArray()
        );

        return response()->streamDownload(
            callback: fn() => $csv->output(),
            name: "inspection-{$this->inspection->name}-data.csv"
        );
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

        // Remove empty values from the custom properties array.
        $this->state['custom_properties']
            = collect($this->state['custom_properties'])
            ->reject(fn($item) => empty($item['key']) && empty($item['value']))
            ->toArray();

        $type = EquipmentType::create(
            Arr::except(array: $this->state, keys: 'custom_properties')
        );
        $type->setCustomProperty(
            name: 'features',
            value: $this->state['custom_properties']
        );
        $type->save();

        $this->resetState();

        $this->equipmentTypesIsEmpty = false;

        $this->equipmentTypes = $this->site->equipmentTypes;

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Successfully created panel\' type equipment')
        ]);
    }

    /**
     * Reset component state.
     *
     * @return void
     */
    public function resetState(): void
    {
        $this->state = [
            'name' => 'Panel',
            'quantity' => 0,
            'custom_properties' => [
                ['key' => '', 'value' => ''],
            ],
        ];
    }

    /**
     * Update inspection tracking data for the current item.
     *
     * @param array $item
     * @return Equipment
     */
    public function setEquipment(array $item = []): Equipment
    {
        $equipment = Equipment::query()->where([
            'equipment_type_id' => $this->state['equipment_type_id'],
            'uuid' => $item['uuid'],
            'name' => $item['name'],
        ])->firstOr(function () use ($item) {
            return Equipment::create([
                'equipment_type_id' => $this->state['equipment_type_id'],
                'uuid' => $item['uuid'],
                'name' => $item['name'],
                'brand' => $item['brand'] ?? null,
                'model' => $item['model'] ?? null,
                'serial' => $item['serial'] ?? null,
            ]);
        });

        // Adds a new custom property or updates an existing one.
        $value = collect($item)->except(['equipment_type_id', 'uuid', 'name'])->toArray();

        $equipment->setCustomProperty("inspections.{$this->inspection->id}", $value)->save();

        return $equipment;
    }

    /**
     * Create annotation for the current equipment.
     *
     * @param array $item
     * @return Annotation
     */
    public function setAnnotation(array $item = []): Annotation
    {
        $record = [
            'user_id' => Auth::id(),
            'annotable_type' => Inspection::class,
            'annotable_id' => $this->inspection->id,
            'content' => __('Automatically generated over imported data.'),
            'custom_properties' => [
                'feature' => [
                    'values' => $item,
                ],
                'title' => "I{$this->inspection->id}-{$item['name']}",
                'status' => 'to_do',
                'priority' => 'high',
                'assignees' => [],
                'commissioning_at' => Carbon::now()->toDateString(),
            ],
        ];

        return Annotation::firstOrCreate(
            [
                'annotable_type' => Inspection::class,
                'annotable_id' => $this->inspection->id,
                'custom_properties->title' => "I{$this->inspection->id}-{$item['name']}"
            ],
            $record
        );
    }
}
