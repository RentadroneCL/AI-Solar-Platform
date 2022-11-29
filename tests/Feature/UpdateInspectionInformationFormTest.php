<?php

use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Models\{Inspection, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Livewire\UpdateInspectionInformationForm;

uses(RefreshDatabase::class);

test('update inspection information form', function () {
    $inspection = Inspection::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(UpdateInspectionInformationForm::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.update-inspection-information-form')
        ->set('state.name', faker()->name)
        ->set('state.commissioning_date', Carbon::now()->toDateTime())
        ->call('update')
        ->assertStatus(200);
});
