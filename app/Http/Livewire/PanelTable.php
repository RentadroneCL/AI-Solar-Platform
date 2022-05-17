<?php

namespace App\Http\Livewire;

use App\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Collection;

class PanelTable extends Component
{
    use WithPagination;

    /**
     * Panels collection.
     *
     * @var Collection
     */
    public Collection $panels;

    /**
     * Event Listeners
     *
     * @var array $listeners
     */
    protected $listeners = [
        'imported'
    ];

    /**
     * Refresh panels collection.
     *
     * @param Site $site
     * @return void
     */
    public function imported(Site $site): void
    {
        $this->panels = $site->panels;
    }
}
