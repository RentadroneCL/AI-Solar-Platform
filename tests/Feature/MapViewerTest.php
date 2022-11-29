<?php

use App\Http\Livewire\MapViewer;
use App\Models\{User, Inspection};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a map viewer livewire component', function () {
    $inspection = Inspection::factory()->create();
    $this->actingAs($user = User::factory()->withPersonalTeam()->create())
        ->livewire(MapViewer::class, ['model' => $inspection, 'files' => $inspection->getMedia('orthomosaic-geojson')])
        ->assertViewIs('livewire.map-viewer')
        ->assertStatus(200);
});

it('updates files collection', function () {
    $inspection = Inspection::factory()->create();
    $this->actingAs($user = User::factory()->withPersonalTeam()->create())
        ->livewire(MapViewer::class, ['model' => $inspection, 'files' => $inspection->getMedia('orthomosaic-geojson')])
        ->assertViewIs('livewire.map-viewer')
        ->call('updateFilesCollection')
        ->assertStatus(200);
});
