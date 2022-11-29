<?php

use App\Models\{User, Site};
use App\Http\Livewire\AssetManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains an asset management livewire component', function () {
    $site = Site::factory()->create();
    $this->actingAs($site->user)
        ->livewire(AssetManagement::class, ['site' => $site])
        ->assertViewIs('livewire.asset-management')
        ->assertSet('site', $site)
        ->assertStatus(200);
});
