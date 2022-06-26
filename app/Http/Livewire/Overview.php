<?php

namespace App\Http\Livewire;

use League\Csv\Writer;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\{Auth, DB};
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\{LazyCollection, Arr, Collection};
use App\Models\{Inspection, Site, Equipment, EquipmentType};

class Overview extends Component
{
    use WithFileUploads;

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
     * Inspection model.
     *
     * @var Inspection
     */
    public Inspection $inspection;

    /**
     * Custom property data.
     *
     * @var Collection
     */
    public Collection $dataset;

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'file' => 'required|file|mimes:csv',
    ];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->site = $this->inspection->site;

        $this->dataset = collect($this->inspection->getCustomProperty('data'));
    }

    /**
     * Import csv file. -> This method need to be moved to a queue
     *
     * @return mixed
     */
    public function import(): mixed
    {
        abort_unless(
            $this->site->isOwner() || Auth::user()->hasRole('administrator'),
            403
        );

        $this->resetErrorBag();

        $this->validate();

        $collection = collect([]);

        // Read CSV file.
        LazyCollection::make(function () {
            $file = fopen($this->file->temporaryUrl(), 'r');

            while ($data = fgetcsv($file)) {
                yield $data;
            }
        })->each(function ($item) use ($collection) {
            $collection->push($item);
        });

        $this->csvHeader = $collection->first();

        $filtered = $collection
            ->skip(1)
            ->map(fn($item) => array_combine($this->csvHeader, $item));

        $quantity = EquipmentType::findOrFail(
            $filtered->first()['equipment_type_id']
        )->quantity;

        $counter = $this->site->equipments()
            ->where(['equipment_type_id' => $filtered->first()['equipment_type_id']])
            ->count();

        try {
            DB::transaction(function () use ($filtered) {
                // Update the inspection metadata.
                $this->inspection->setCustomProperty('data', $filtered)->save();

                // Update inspection tracking data for the current item.
                $filtered->map(function ($item) {
                    $equipment = Equipment::query()->where([
                        'equipment_type_id' => $item['equipment_type_id'],
                        'uuid' => $item['uuid'],
                        'name' => $item['name'],
                    ])->firstOr(function () use ($item) {
                        $newEquipment = Equipment::create([
                            'equipment_type_id' => $item['equipment_type_id'],
                            'uuid' => $item['uuid'],
                            'name' => $item['name'],
                            'brand' => $item['brand'] ?? null,
                            'model' => $item['model'] ?? null,
                            'serial' => $item['serial'] ?? null,
                        ]);

                        return $newEquipment;
                    });

                    // Adds a new custom property or updates an existing one.
                    $value = collect($item)
                        ->except(['equipment_type_id', 'uuid', 'name'])
                        ->toArray();

                    $equipment->setCustomProperty("inspections.{$this->inspection->id}", $value)->save();
                });
            });

            return redirect()->route('inspection.show', $this->inspection);

        } catch (\Throwable $th) {
            $this->showImportModal = false;
            $this->emit('error', $th->getMessage());
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
     * Updated file property.
     *
     * @return void
     */
    public function updatedFile(): void
    {
        $this->filename = $this->file->temporaryUrl();
    }
}
