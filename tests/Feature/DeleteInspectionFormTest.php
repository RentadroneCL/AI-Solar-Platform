<?php

use App\Models\{User, Inspection};
use App\Http\Livewire\DeleteInspectionForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a delete inspection form livewire component', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteInspectionForm::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.delete-inspection-form')
        ->assertStatus(200);
});

it('display modal for confirming inspection deletion', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteInspectionForm::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.delete-inspection-form')
        ->call('confirmInspectionDeletion')
        ->assertSet('password', '')
        ->assertDispatchedBrowserEvent('confirming-delete-inspection')
        ->assertSet('confirmingInspectionDeletion', true)
        ->assertStatus(200);
});

it('can delete an inspection', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteInspectionForm::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.delete-inspection-form')
        ->set('password', 'password')
        ->call('destroy')
        ->assertStatus(200);
});

it('fails with the wrong password', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteInspectionForm::class, ['inspection' => $inspection])
        ->set('password', 'wrong password')
        ->call('destroy')
        ->assertHasErrors('password')
        ->assertStatus(200);
});
