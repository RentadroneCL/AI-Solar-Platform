<?php

use App\Models\{User, Site};
use function Pest\Faker\faker;
use App\Http\Livewire\SitesCardGrid;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a site card grid livewire component', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user)
        ->livewire(SitesCardGrid::class)
        ->assertViewIs('livewire.sites-card-grid')
        ->assertStatus(200);
});

it('can search for a specified site', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user)
        ->livewire(SitesCardGrid::class)
        ->assertViewIs('livewire.sites-card-grid')
        ->set('query', faker()->name)
        ->call('search')
        ->assertStatus(200);
});

it('can an administrator search for all sites', function () {
    $user = User::factory()
                ->withPersonalTeam()
                ->create()
                ->assignRole('administrator');

    $this->actingAs($user)
        ->livewire(SitesCardGrid::class)
        ->assertViewIs('livewire.sites-card-grid')
        ->set('query', faker()->name)
        ->call('search')
        ->assertStatus(200);
});
