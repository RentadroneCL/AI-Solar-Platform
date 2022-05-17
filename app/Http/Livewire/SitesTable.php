<?php

namespace App\Http\Livewire;

use App\Models\Site;
use Livewire\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class SitesTable extends Component
{
    /**
     * Sites collection.
     *
     * @var \Illuminate\Database\Eloquent\Collection $sites
     */
    public Collection $sites;
}
