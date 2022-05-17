<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Livewire\WithFileUploads;
use App\Models\{Site, Panel};
use League\Csv\{Writer, Reader};
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetManagement extends Component
{
    use WithFileUploads;

    /**
     * CSV file.
     */
    public $file;

    /**
     * Site model.
     *
     * @var \App\Models\Site $site
     */
    public Site $site;

    /**
     * Display import CSV modal.
     *
     * @var boolean
     */
    public bool $showImportModal = false;

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
    public array $csvHeader = [
        'panel_id',
        'panel_serial',
        'panel_zone',
        'panel_sub_zone',
        'panel_string',
    ];

    /**
     * Panels collection.
     *
     * @var Collection
     */
    public Collection $panels;

    /**
     * Validation rules.
     *
     * @var array
     */
    protected array $rules = [
        'file' => 'required|file|mimes:csv',
    ];

    /**
     * Event listeners.
     *
     * @var array
     */
    // protected $listeners = ['saved'];

    /**
     * Set the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        //$this->panels = $this->site->panels;
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
     * Import CSV file.
     *
     * @return void
     */
    public function import(): void
    {
        $this->resetErrorBag();

        $this->validate();

        $content = file_get_contents($this->file->temporaryUrl());

        $csv = Reader::createFromString((string) $content);
        $csv->setHeaderOffset(0);
        $csv->setEscape('\\');
        $csv->setDelimiter(',');

        $records = collect($csv->getRecords())
            ->map(fn($item) => Arr::add($item, 'site_id', $this->site->id));

        try {
            DB::transaction(function () use ($records) {
                $records->map(fn($item) => Panel::updateOrCreate($item, $item));
            });

            $this->showImportModal = false;
            $this->emit('imported', $this->site);

        } catch (\Throwable $th) {
            $this->showImportModal = false;
            $this->emit('error');
        }
    }

    /**
     * Export CSV data.
     *
     * @return StreamedResponse
     */
    public function export(): StreamedResponse
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne($this->csvHeader);
        $csv->insertAll(
            $this->panels->map(fn($item) => $item->only($this->csvHeader))->toArray()
        );

        return response()->streamDownload(fn() => $csv->output(), 'assets-management.csv');
    }

    /**
     * Dispatching Events.
     *
     * @return void
     */
    /* public function saved(): void
    {
        $this->emit('saved');
    } */
}
