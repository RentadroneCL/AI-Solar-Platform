<?php

namespace App\Http\Livewire;

use App\Models\{Inspection, Site};
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;

class InspectionTable extends DataTableComponent
{
    /**
     * Site model.
     *
     * @var Site
     */
    public Site $site;

    /**
     * Calls the query method on the model.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return Inspection::query()
            ->leftJoin('sites_information', 'sites_information.id', '=', 'inspections_information.site_id')
            ->select('inspections_information.id', 'inspections_information.name', 'inspections_information.commissioning_date')
            ->where(['site_id' => $this->site->id]);
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
            Column::make("Commissioning date", "commissioning_date")
                ->sortable()
                ->searchable(),
            ButtonGroupColumn::make(__('Actions'))
                ->buttons([
                    LinkColumn::make(__('Manage'))
                        ->title(fn($row) => __('Manage'))
                        ->attributes(function ($row) {
                            return [
                                'class' => 'text-slate-400 hover:text-slate-500 hover:underline',
                            ];
                        })
                        ->location(fn($row) => route('inspection.show', $row)),
                ]),
        ];
    }
}
