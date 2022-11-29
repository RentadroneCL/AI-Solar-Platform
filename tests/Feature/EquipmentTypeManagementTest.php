<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\EquipmentTypeManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Site, Equipment, EquipmentType};

use function Pest\Faker\faker;

uses(RefreshDatabase::class);

it('contains a equipment type management livewire component', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-type-management')
        ->assertStatus(200);
});

it('administrator or owner can store a newly created resource in storage', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-type-management')
        ->set('state', [
            'site_id' => $site->id,
            'name' => faker()->name,
            'quantity' => faker()->randomNumber(5, true),
            'custom_properties' => [
                ['key' => 'foo', 'value' => 'bar'],
            ]
        ])
        ->call('store')
        ->assertSet('displayEquipmentTypeCreationForm', false)
        ->assertEmitted('saved-type-row')
        ->assertStatus(200);
});

it('confirms that the administrator or owner would like to edit the equipment type', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-type-management')
        ->call('edit', $type->toArray())
        ->assertSet('confirmingEquipmentTypeEdition', true)
        ->assertStatus(200);
});

it('confirms that the administrator or owner would like to edit the equipment type with customs properties', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $state = [...$type->toArray()];
    $state['custom_properties']['features'] = [
        ['key' => 'foo', 'value' => 'bar']
    ];
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-type-management')
        ->call('edit', $state)
        ->assertSet('confirmingEquipmentTypeEdition', true)
        ->assertStatus(200);
});

it('can administrator or owner update the specified resource in storage', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $state = [...$type->toArray()];
    $state['custom_properties'] = [
        ['key' => 'foo', 'value' => 'bar']
    ];
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-type-management')
        ->set('state', $state)
        ->call('update')
        ->assertEmitted('edited-type-row')
        ->assertSet('confirmingEquipmentTypeEdition', false)
        ->assertStatus(200);
});

it('fails when an administrator or owner update tries to update specified resource in storage', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $state = [...$type->toArray()];
    $state['custom_properties'] = [
        ['key' => 'foo', 'value' => 'bar']
    ];
    $state['id'] = rand();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-type-management')
        ->set('state', $state)
        ->call('update')
        ->assertEmitted('error')
        ->assertSet('confirmingEquipmentTypeEdition', false)
        ->assertStatus(200);
});

it('confirms that the administrator or owner would like to delete the equipment type', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-type-management')
        ->call('delete', $type->toArray())
        ->assertSet('confirmingEquipmentTypeDeletion', true)
        ->assertStatus(200);
});

it('administrator or owner can delete the specified resource in storage', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-type-management')
        ->set('state', [...$type->toArray()])
        ->set('password', 'password')
        ->call('destroy')
        ->assertSet('password', '')
        ->assertSet('confirmingEquipmentTypeDeletion', false)
        ->assertEmitted('deleted-type-row')
        ->assertStatus(200);
});

it('fails when the administrator or owner tries to delete the specified resource in storage with the wrong password', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-type-management')
        ->set('state', [...$type->toArray()])
        ->set('password', 'wrong-password')
        ->call('destroy')
        ->assertHasErrors('password')
        ->assertStatus(200);
});

it('can reset the component state', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-type-management')
        ->set('state', [
            'id' => $type->id,
            'name' => $type->name,
            'quantity' => $type->quantity,
            'custom_properties' => $type->custom_properties,
        ])
        ->call('resetState')
        ->assertSet('state', [
            'name' => '',
            'quantity' => 0,
            'custom_properties' => [
                ['key' => '', 'value' => ''],
            ],
        ])
        ->assertStatus(200);
});

it('can create dynamic input fields', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-type-management')
        ->call('addCustomPropertyInput')
        ->assertSet('state.custom_properties', [
            ['key' => '', 'value' => ''],
            ['key' => '', 'value' => ''],
        ])
        ->assertStatus(200);
});

it('it can remove specific input field', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-type-management')
        ->call('addCustomPropertyInput')
        ->assertSet('state.custom_properties', [
            ['key' => '', 'value' => ''],
            ['key' => '', 'value' => ''],
        ])
        ->call('removeCustomPropertyInput', 0)
        ->assertEmitted('removed-custom-property')
        ->assertStatus(200);
});

it('can export CSV data', function () {
    $user = User::factory()->create();
    $type = EquipmentType::factory()->count(10)->create();
    $site = $type->first()->site;
    $this->actingAs($user)
        ->livewire(EquipmentTypeManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-type-management')
        ->call('export')
        ->assertStatus(200);
});
