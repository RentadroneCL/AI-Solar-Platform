<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\{Annotation, Inspection};
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;

class InspectionAnnotationTable extends DataTableComponent
{
    /**
     * Inspection model.
     *
     * @var Inspection
     */
    public Inspection $inspection;

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = [
        'delete-annotation' => 'delete',
        'updated-annotation-row' => '$refresh',
        'deleted-annotation-row' => '$refresh',
    ];

    /**
     * Calls the query method on the model.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return Annotation::query()
            ->with(['user'])
            ->select('id', 'content', 'custom_properties', 'created_at', 'updated_at')
            ->where([
                'annotable_type' => Inspection::class,
                'annotable_id' => $this->inspection->id,
            ]);
    }

    /**
     * Component configuration.
     *
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setBulkActions([
                'destroySelected' => __('Delete selected'),
            ]);
    }

    /**
     * Column objects in the order you wish to see them on the table.
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make(__('Title'), "custom_properties->title")
                ->sortable(),
            Column::make(__('Status'), "custom_properties->status")
                ->format(function ($value, $row, Column $column) {
                    switch ($value) {
                        case 'to_do':
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"secondary-badge\"><i class=\"fa-solid fa-tag fa-fw\"></i><span>".Str::ucfirst(__('to do'))."</span></div></div>";
                        case 'pending':
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"warning-badge\"><i class=\"fa-solid fa-tag fa-fw\"></i><span>".Str::ucfirst(__('pending'))."</span></div></div>";
                        case 'in_progress':
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"info-badge\"><i class=\"fa-solid fa-tag fa-fw\"></i><span>".Str::ucfirst(__('in progress'))."</span></div></div>";
                        case 'delayed':
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"danger-badge\"><i class=\"fa-solid fa-tag fa-fw\"></i><span>".Str::ucfirst(__('delayed'))."</span></div></div>";
                        default:
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"success-badge\"><i class=\"fa-solid fa-tag fa-fw\"></i><span>".Str::ucfirst(__('done'))."</span></div></div>";
                    }
                })
                ->html()
                ->sortable(),
            Column::make(__('Assigned'), "custom_properties->assignees")
                ->format(function ($value, $row, Column $column) {
                    $assignees = '';

                    foreach (json_decode($value, true) as $item) {
                        $assignees.= "<img class=\"avatar\" src=\"{$item['profile_photo_url']}\" alt=\"{$item['name']}\" title=\"{$item['name']}\"/>";
                    }

                    return <<<EOT
                        <div class="assignees-avatars">
                            {$assignees}
                        </div>
                    EOT;
                })
                ->html(),
            Column::make(__('Priority'), "custom_properties->priority")
                ->format(function ($value, $row, Column $column) {
                    switch ($value) {
                        case 'high':
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"danger-badge\"><span>".Str::ucfirst($value)."</span></div></div>";
                        case 'medium':
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"warning-badge\"><span>".Str::ucfirst($value)."</span></div></div>";
                        case 'low':
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"info-badge\"><span>".Str::ucfirst($value)."</span></div></div>";
                        default:
                            return "<div class=\"simplemap-tables-badge-column\"><div class=\"secondary-badge\"><span>".Str::ucfirst($value)."</span></div></div>";
                    }
                })
                ->html()
                ->sortable(),
            Column::make(__('Commissioning'), "custom_properties->commissioning_at")
                ->format(
                    fn($value, $row, Column $column) => $value->toFormattedDateString()
                )
                ->sortable(),
            Column::make(__('Updated'), "updated_at")
                ->format(
                    fn($value, $row, Column $column) => $value->diffForHumans()
                )
                ->sortable(),
            ButtonGroupColumn::make(__('Actions'))
                ->buttons([
                    LinkColumn::make(__('Manage'))
                        ->title(fn($row) => __('Manage'))
                        ->attributes(fn($row) => ['class' => 'text-slate-400 hover:text-slate-500 hover:underline', '@click.prevent' => "\$wire.emit('edit-annotation', {$row})"])
                        ->location(fn($row) => '#'),
                    LinkColumn::make(__('Delete'))
                        ->title(fn($row) => __('Delete'))
                        ->attributes(fn($row) => [ 'class' => 'text-rose-400 hover:text-rose-500 hover:underline', '@click.prevent' => "\$wire.emit('delete-annotation', {$row})"])
                        ->location(fn($row) => '#'),
                ]),

        ];
    }

    /**
     * Filter types to choose from.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            SelectFilter::make(__('Status'))
                ->options([
                    '' => __('Default'),
                    'to_do' => __('To do'),
                    'pending' => __('Pending'),
                    'in_progress' => __('In progress'),
                    'delayed' => __('Delayed'),
                    'done' => __('Done'),
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->whereJsonContains('custom_properties->status', $value);
                    }
                }),
            SelectFilter::make(__('Priority'))
                ->options([
                    '' => __('Default'),
                    'high' => __('High'),
                    'medium' => __('Medium'),
                    'low' => __('Low'),
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->whereJsonContains('custom_properties->priority', $value);
                    }
                }),
            SelectFilter::make(__('Fail type'))
                ->options([
                    '' => __('Default'),
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
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->whereJsonContains('custom_properties->feature->values->failCode', $value);
                    }
                }),
            SelectFilter::make(__('Severity'))
                ->options([
                    '' => __('Default'),
                    1 => __('Low / Minor'),
                    2 => __('Middle / Major'),
                    3 => __('High / Critical'),
                    4 => __('Indeterminate'),
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->whereJsonContains('custom_properties->feature->values->severity', $value);
                    }
                }),
            DateFilter::make(__('Commissioning at'))
                ->filter(fn(Builder $builder, string $value) => $builder->whereJsonContains('custom_properties->commissioning_at', $value)),
        ];
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param array $row
     * @return void
     */
    public function delete(array $row = []): void
    {
        try {
            abort_unless($this->inspection->site->isOwner() || Auth::user()->hasRole('administrator'), 403);
            $this->emitUp('delete-annotation-row', $row);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('You don\'t have permission to perform this operation.')
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function destroySelected(): void
    {
        try {
            abort_unless($this->inspection->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

            if (empty($this->getSelected())) {
                return;
            }

            Annotation::destroy($this->getSelected());

            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Successfully deleted annotations.')
            ]);

            $this->clearSelected();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => __('You don\'t have permission to perform this operation.')
            ]);
        }
    }
}
