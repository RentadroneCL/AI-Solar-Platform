<?php

use App\Http\Livewire\InspectionTable;
use App\Models\{User, Inspection, Site};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a inspection table livewire component', function () {
    $site = Site::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(InspectionTable::class, ['site' => $site, 'model' => Inspection::class])
        ->assertViewIs('livewire-tables::datatable')
        ->assertStatus(200);
});
