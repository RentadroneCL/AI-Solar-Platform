<?php

namespace App\Http\Livewire;

use App\Models\Site;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SitesCardGrid extends Component
{
    /**
     * Sites search criteria.
     *
     * @var string
     */
    public string $query = '';

    /**
     * Sites collection.
     *
     * @var Collection|null
     */
    public ?Collection $sites;

    /**
     * Set component state.
     *
     * @return void
     */
    public function mount(): void
    {
        // If the user is the root admin load all sites available.
        if (Auth::user()->hasRole('administrator')) {
            $this->sites = Site::query()
                ->with('inspections')
                ->select('id', 'name', 'latitude', 'longitude')
                ->get();
        } else {
            // Sites are loading in the scope of the current team selected.
            if (Auth::user()->currentTeam->id === Auth::user()->personalTeam()->id) {
                $this->sites = Site::query()->with('inspections')
                    ->rightJoin('users', 'users.id', '=', 'sites_information.user_id')
                    ->select('sites_information.id', 'sites_information.name', 'latitude', 'longitude')
                    ->where(['sites_information.user_id' => Auth::id()])
                    ->get();
            } else {
                $this->sites = Site::query()->with('inspections')
                    ->rightJoin('users', 'users.id', '=', 'sites_information.user_id')
                    ->select('sites_information.id', 'sites_information.name', 'latitude', 'longitude')
                    ->where(['sites_information.user_id' => Auth::user()->currentTeam->user_id])
                    ->get();
            }
        }
    }

    /**
     * Data filtered.
     *
     * @return void
     */
    public function search(): void
    {
        if (Auth::user()->hasRole('administrator')) {
            $filtered = Site::query()->with(['inspections'])
                ->where(function (Builder $query) {
                    $query->where('name', 'like', '%' . $this->query . '%');
                })
                ->get();

            $this->sites = $filtered;
        } else {
            $filtered = Site::query()->with(['inspections'])
                ->rightJoin('users', 'users.id', '=', 'sites_information.user_id')
                ->select('sites_information.id', 'sites_information.name', 'latitude', 'longitude')
                ->where(['sites_information.user_id' => Auth::user()->currentTeam->user_id])
                ->where(function (Builder $query) {
                    $query->where('sites_information.name', 'like', '%' . $this->query . '%');
                })
                ->get();

            $this->sites = $filtered;
        }
    }
}
