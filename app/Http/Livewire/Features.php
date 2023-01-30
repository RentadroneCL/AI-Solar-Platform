<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\{Inspection, Annotation, Media};

class Features extends Component
{
    /**
     * Display features state.
     *
     * @var boolean
     */
    public bool $showDropdown = false;

    /**
     * Check for file image.
     *
     * @var boolean
     */
    public bool $hasMediaFile = false;

    /**
     * Features values.
     *
     * @var array
     */
    public array $feature = [];

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = [
        'edit-annotation' => 'setFeature',
    ];

    /**
     * Runs after a property are updated.
     *
     * @param array $payload
     *
     * @return void
     */
    public function setFeature(array $payload = []): void
    {
        if (Arr::has($payload, 'custom_properties.feature.values')) {
            $this->feature = Arr::get($payload, 'custom_properties.feature.values');
            $this->feature['failCode'] = $this->getFailType($this->feature['failCode']);
            $this->feature['severity'] = $this->getSeverityLevel($this->feature['severity']);
            $this->feature['annotable_id'] = $payload['id'];

            if (array_key_exists('filename', $this->feature)) {
                $this->getImageFile($this->feature['filename']);
            }
        }
    }

    /**
     * Get the severity level.
     *
     * @param integer|null $value
     *
     * @return string
     */
    public function getSeverityLevel(int $value = null): string
    {
        $level = [
            1 => __('Low / Minor'),
            2 => __('Middle / Major'),
            3 => __('High / Critical'),
            4 => __('Indeterminate'),
        ];

        return $level[$value] ?? 'N/A';
    }

    /**
     * Get the type of failure.
     *
     * @param integer|null $value
     *
     * @return string
     */
    public function getFailType(int $value = null): string
    {
        $type = [
            1 => __('AN AFFECTED CELL OR CONNECTION'),
            2 => __('2 TO 4 CELLS AFFECTED'),
            3 => __('5 OR MORE CELLS AFFECTED'),
            4 => __('BYPASS DIODE'),
            5 => __('DISCONNECTED / DEACTIVATED SINGLE PANEL'),
            6 => __('CONNECTIONS OR OTHERS'),
            7 => __('SOILING / DIRTY'),
            8 => __('DAMAGED TRACKER'),
            9 => __('SHADOWING'),
            10 => __('MISSING PANEL'),
            11 => __('DISCONNECTED / DEACTIVATED STRING'),
            12 => __('DISCONNECTED / DEACTIVATED ZONE'),
            13 => __('HOT SPOT SINGLE'),
            14 => __('HOT SPOT MULTI'),
            15 => __('BYPASS DIODE MULTI'),
        ];

        return $type[$value] ?? 'N/A';
    }

    /**
     * Retrieve the image file.
     *
     * @param string $filename
     *
     * @return void
     */
    public function getImageFile(string $filename = ''): void
    {
        try {
            $annotation = Annotation::findOrFail($this->feature['annotable_id']);

            $media = Media::query()->where(function (Builder $query) use ($filename, $annotation) {
                $query->where([
                    'model_type' => Inspection::class,
                    'model_id' => $annotation->annotable_id,
                    'collection_name' => 'ir',
                    'name' => $filename,
                ])->orWhere(function (Builder $query) use ($filename, $annotation) {
                    $query->where([
                        'model_type' => Inspection::class,
                        'model_id' => $annotation->annotable_id,
                        'collection_name' => 'rgb',
                        'name' => $filename,
                    ]);
                })->orWhere(function (Builder $query) use ($filename, $annotation) {
                    $query->where([
                        'model_type' => Inspection::class,
                        'model_id' => $annotation->annotable_id,
                        'collection_name' => 'default',
                        'name' => $filename,
                    ]);
                });
            })->firstOrFail();

            $this->feature['img'] = [
                'file_name' => $media->file_name,
                'file_url' => Storage::temporaryUrl($media->getPath(), now()->addMinutes(60)),
                'name' => $media->name,
                'size' => $media->human_readable_size,
            ];

            $this->hasMediaFile = true;

        } catch (\Throwable $th) {
            $this->hasMediaFile = false;
        }
    }
}
