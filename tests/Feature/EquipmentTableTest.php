<?php

use App\Http\Livewire\EquipmentTable;
use App\Models\{User, Site, Equipment};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains an equipments table livewire component', function () {
    $site = Site::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(EquipmentTable::class, ['site' => $site, 'model' => Equipment::class])
        ->assertViewIs('livewire-tables::datatable')
        ->assertStatus(200);
});

it('administrator can click edit - emit an event to edit row', function () {
    $site = Site::factory()->create();
    $user = User::factory()->create()->assignRole('administrator');
    $row = Equipment::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTable::class, ['site' => $site, 'model' => Equipment::class])
        ->assertViewIs('livewire-tables::datatable')
        ->call('edit', $row->toArray())
        ->assertEmitted('edit-row', $row->toArray())
        ->assertStatus(200);
});

it('administrator can click delete - emit an event to delete row', function () {
    $site = Site::factory()->create();
    $user = User::factory()->create()->assignRole('administrator');
    $row = Equipment::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTable::class, ['site' => $site, 'model' => Equipment::class])
        ->assertViewIs('livewire-tables::datatable')
        ->call('delete', $row->toArray())
        ->assertEmitted('delete-row', $row->toArray())
        ->assertStatus(200);
});
