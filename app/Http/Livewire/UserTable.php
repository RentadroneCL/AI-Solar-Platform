<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class UserTable extends Component
{
    /**
     * Users collection.
     *
     * @var Collection
     */
    public Collection $users;
}
