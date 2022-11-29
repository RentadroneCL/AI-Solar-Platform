<?php

use App\Models\{Site, User};
use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Http\Livewire\UpdateSiteInformationForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('update site information form', function () {
    $site = Site::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(UpdateSiteInformationForm::class, ['site' => $site])
        ->assertViewIs('livewire.update-site-information-form')
        ->set('state.name', faker()->name)
        ->set('state.address', faker()->address)
        ->set('state.latitude', faker()->latitude($min = -90, $max = 90))
        ->set('state.longitude', faker()->longitude($min = -180, $max = 180))
        ->set('state.commissioning_date', Carbon::now()->toDateTime())
        ->call('update')
        ->assertStatus(200);
});
