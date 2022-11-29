<?php

use App\Models\{User, Site};
use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Http\Livewire\SiteManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a site management livewire component', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(SiteManagement::class)
        ->assertViewIs('livewire.site-management')
        ->assertStatus(200);
});

it('fetch all sites if user is an administrator', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $this->actingAs($user)
        ->livewire(SiteManagement::class)
        ->assertViewIs('livewire.site-management')
        ->assertStatus(200);
});

it('search user data - search drop-down', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $this->actingAs($user)
        ->livewire(SiteManagement::class)
        ->assertViewIs('livewire.site-management')
        ->set('query', faker()->name)
        ->call('search')
        ->assertStatus(200);
});

it('set site owner property.', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $owner = User::factory()->create();
    $this->actingAs($user)
        ->livewire(SiteManagement::class)
        ->assertViewIs('livewire.site-management')
        ->call('selectedOwner', $owner)
        ->assertSet('owner', $owner)
        ->assertSet('state.user_id', $owner->id)
        ->assertSet('users', null)
        ->assertSet('query', '')
        ->assertEmitted('selected')
        ->assertStatus(200);
});

it('discard owner selection', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $owner = User::factory()->create();
    $this->actingAs($user)
        ->livewire(SiteManagement::class)
        ->assertViewIs('livewire.site-management')
        ->call('selectedOwner', $owner)
        ->assertSet('owner', $owner)
        ->call('discardSelection')
        ->assertSet('owner', null)
        ->assertSet('state.user_id', null)
        ->assertEmitted('discard-selected')
        ->assertStatus(200);
});

it('can create a new site', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $owner = User::factory()->create();
    $this->actingAs($user)
        ->livewire(SiteManagement::class)
        ->assertViewIs('livewire.site-management')
        ->call('selectedOwner', $owner)
        ->assertSet('owner', $owner)
        ->set('state.user_id', $owner->id)
        ->set('state.address', faker()->address)
        ->set('state.latitude', faker()->latitude($min = -90, $max = 90))
        ->set('state.longitude', faker()->longitude($min = -180, $max = 180))
        ->set('state.commissioning_date', Carbon::now()->toDateTime())
        ->call('store')
        ->assertStatus(200);
});
