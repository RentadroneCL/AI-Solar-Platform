<?php

use App\Models\User;
use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Http\Livewire\CreateSiteForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create site form', function () {
    $this->actingAs($user = User::factory()->create())
        ->livewire(CreateSiteForm::class)
        ->assertViewIs('livewire.create-site-form')
        ->set('state.name', faker()->name)
        ->set('state.address', faker()->address)
        ->set('state.latitude', faker()->latitude($min = -90, $max = 90))
        ->set('state.longitude', faker()->longitude($min = -180, $max = 180))
        ->set('state.commissioning_date', Carbon::now()->toDateTime())
        ->call('store')
        ->assertStatus(200);
});
