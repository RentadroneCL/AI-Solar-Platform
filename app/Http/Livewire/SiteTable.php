<?php

namespace App\Http\Livewire;

use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;

class SiteTable extends DataTableComponent
{
    /**
     * The columns passed to the select method.
     *
     * @var array
     */
    protected array $selectStatement = [
        'sites_information.id',
        'sites_information.name',
        'address',
        'latitude',
        'longitude',
        'sites_information.created_at',
        'sites_information.updated_at',
    ];

    /**
     * Calls the query method on the model.
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        if (Auth::user()->hasRole('administrator')) {
            return Site::query()
                ->with('user')
                ->select($this->selectStatement);
        }

        return Site::query()
            ->with('user')
            ->leftJoin('users', 'users.id', '=', 'sites_information.user_id')
            ->select($this->selectStatement)
            ->where(['sites_information.user_id' => Auth::id()]);
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
            Column::make("Owner", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Contact", "user.email")
                ->sortable()
                ->searchable(),
            Column::make("Name", "name")
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
                        ->location(fn($row) => route('site.show', $row)),
                ]),
        ];
    }
}
