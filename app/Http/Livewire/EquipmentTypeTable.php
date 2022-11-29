<?php

namespace App\Http\Livewire;

use App\Models\{EquipmentType, Site};
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;

class EquipmentTypeTable extends DataTableComponent
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
        'edit-type' => 'edit',
        'delete-type' => 'delete',
        'saved-type-row' => '$refresh',
        'edited-type-row' => '$refresh',
        'deleted-type-row' => '$refresh',
    ];

    /**
     * Calls the query method on the model.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return EquipmentType::query()
            ->leftJoin('sites_information', 'sites_information.id', '=', 'equipment_type.site_id')
            ->select('equipment_type.name', 'equipment_type.id', 'quantity', 'custom_properties')
            ->where(['sites_information.id' => $this->site->id]);
    }

    /**
     * Component configuration.
     *
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey('id');
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
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Quantity", "quantity")
                ->sortable(),
            ButtonGroupColumn::make(__('Actions'))
                ->buttons([
                    LinkColumn::make(__('Edit'))
                        ->title(fn($row) => __('Edit'))
                        ->attributes(fn($row) => ['class' => 'text-slate-400 hover:text-slate-500 hover:underline', 'x-on:click' => "\$wire.emit('edit-type', {$row})"])
                        ->location(fn($row) => '#'),
                    LinkColumn::make(__('Delete'))
                        ->title(fn($row) => __('Delete'))
                        ->attributes(fn($row) => ['class' => 'text-rose-400 hover:text-rose-500 hover:underline', 'x-on:click' => "\$wire.emit('delete-type', {$row})"])
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

        $this->emitUp('edit-type-row', $row);
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return void
     */
    public function delete(array $row): void
    {
        abort_unless($this->site->isOwner() || Auth::user()->hasRole('administrator'), 403);

        $this->emitUp('delete-type-row', $row);
    }
}
