<?php

use App\Http\Livewire\EquipmentTypeTable;
use App\Models\{User, Site, EquipmentType};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains an equipments types table livewire component', function () {
    $site = Site::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(EquipmentTypeTable::class, ['site' => $site, 'model' => EquipmentType::class])
        ->assertViewIs('livewire-tables::datatable')
        ->assertStatus(200);
});

it('administrator can click edit - emit an event to edit row', function () {
    $site = Site::factory()->create();
    $user = User::factory()->create()->assignRole('administrator');
    $row = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeTable::class, ['site' => $site, 'model' => EquipmentType::class])
        ->assertViewIs('livewire-tables::datatable')
        ->call('edit', $row->toArray())
        ->assertEmitted('edit-type-row', $row->toArray())
        ->assertStatus(200);
});

it('administrator can click delete - emit an event to delete row', function () {
    $site = Site::factory()->create();
    $user = User::factory()->create()->assignRole('administrator');
    $row = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeTable::class, ['site' => $site, 'model' => EquipmentTypeTable::class])
        ->assertViewIs('livewire-tables::datatable')
        ->call('delete', $row->toArray())
        ->assertEmitted('delete-type-row', $row->toArray())
        ->assertStatus(200);
});
