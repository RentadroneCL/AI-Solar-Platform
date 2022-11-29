<?php

namespace App\Http\Livewire;

use App\Models\{Equipment, Site};
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;

class EquipmentTable extends DataTableComponent
{
    /**
     * Site model.
     *
     * @var Site
     */
    public Site $site;

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = [
        'edit',
        'delete',
        'saved-row' => '$refresh',
        'edited-row' => '$refresh',
        'deleted-row' => '$refresh',
    ];

    /**
     * Calls the query method on the model.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return Equipment::query()
            ->with('type')
            ->leftJoin('equipment_type', 'equipment_type.id', '=', 'equipments.equipment_type_id')
            ->select('equipments.id', 'equipment_type.name as type', 'equipments.name', 'brand', 'model', 'serial', 'equipments.custom_properties')
            ->where(['equipment_type.site_id' => $this->site->id]);
    }

    /**
     * Component configuration.
     *
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey('equipments.id');

        // Perform methods on selected rows.
        // $this->setBulkActions([
        //     'delete' => __('Delete'),
        // ]);
    }

    /**
     * Column objects in the order you wish to see them on the table.
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable(),
            Column::make('Type', 'type.name')
                ->sortable(),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Brand', 'brand')
                ->sortable()
                ->searchable(),
            Column::make('Model', 'model')
                ->sortable()
                ->searchable(),
            Column::make('Serial', 'serial')
                ->sortable()
                ->searchable(),
            ButtonGroupColumn::make(__('Actions'))
                ->buttons([
                    LinkColumn::make(__('Edit'))
                        ->title(fn($row) => __('Edit'))
                        ->attributes(fn($row) => ['class' => 'text-slate-400 hover:text-slate-500 hover:underline', 'x-on:click' => "\$wire.emit('edit', {$row})"])
                        ->location(fn($row) => '#'),
                    LinkColumn::make(__('Delete'))
                        ->title(fn($row) => __('Delete'))
                        ->attributes(fn($row) =>['class' => 'text-rose-400 hover:text-rose-500 hover:underline', 'x-on:click' => "\$wire.emit('delete', {$row})"])
                        ->location(fn($row) => '#'),
                ]),
        ];
    }

    /**
     * Confirm that the user would like to edit the equipment.
     *
     * @param array $row
     * @return void
     */
    public function edit(array $row): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->emitUp('edit-row', $row);
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return void
     */
    public function delete(array $row): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->emitUp('delete-row', $row);
    }
}
