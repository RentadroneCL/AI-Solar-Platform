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
        if (Auth::user()->hasRole('administrator')) {
            $this->sites = Site::all();
        } else {
            $this->sites = Auth::user()->sites;
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
            $filtered = Auth::user()->sites()
                ->with(['inspections'])
                ->where(function (Builder $query) {
                    $query->where('name', 'like', '%' . $this->query . '%');
                })
                ->get();

            $this->sites = $filtered;
        }
    }
}
