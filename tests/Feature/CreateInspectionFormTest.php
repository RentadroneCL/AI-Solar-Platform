<?php

use App\Models\{User, Site};
use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Http\Livewire\CreateInspectionForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains an inspection form livewire component', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(CreateInspectionForm::class, ['site' => $site])
        ->assertViewIs('livewire.create-inspection-form');
});

it('can create a new inspection', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(CreateInspectionForm::class, ['site' => $site])
        ->set('state.name', faker()->name)
        ->set('state.commissioning_date', Carbon::now()->toDateTime())
        ->call('store')
        ->assertStatus(200);
});
