<?php

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\EquipmentManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Site, Equipment, EquipmentType};

use function Pest\Faker\faker;

uses(RefreshDatabase::class);

it('contains a equipment management livewire component', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->assertStatus(200);
});

it('display modal for equipment creation form', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->toggle('displayEquipmentCreationForm')
        ->assertSet('displayEquipmentCreationForm', true)
        ->assertStatus(200);
});

it('confirms that the administrator or owner would like to edit the equipment', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $equipment = Equipment::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('edit', $equipment->toArray())
        ->assertSet('confirmingEquipmentEdition', true)
        ->assertStatus(200);
});

it('confirms that the administrator or owner would like to edit the equipment with customs properties', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $equipment = Equipment::factory()->create();
    $site = Site::factory()->create();
    $state = [...$equipment->toArray()];
    $state['custom_properties']['features'] = [
        ['key' => 'foo', 'value' => 'bar']
    ];
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('edit', $state)
        ->assertSet('confirmingEquipmentEdition', true)
        ->assertStatus(200);
});

it('administrator or owner can update the specified resource in storage', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $equipment = Equipment::factory()->create();
    $type = $equipment->type;
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->set('state', $equipment->toArray())
        ->set('state.custom_properties', [['key' => '', 'value' => '']])
        ->call('update')
        ->assertSet('confirmingEquipmentEdition', false)
        ->assertStatus(200);
});

it('administrator or owner can store a newly created resource in storage', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $type->site])
        ->assertViewIs('livewire.equipment-management')
        ->call('selectedEquipmentType', $type)
        ->assertSet('equipmentType', $type)
        ->set('state', [
            'equipment_type_id' => $type->id,
            'name' => faker()->name,
            'custom_properties' => [
                ['key' => null, 'value' => null]
            ]
        ])
        ->call('store')
        ->assertSet('displayEquipmentCreationForm', false)
        ->assertSet('equipmentType', null)
        ->assertEmitted('saved-row')
        ->assertStatus(200);
});

it('confirms that the administrator or owner would like to delete the equipment', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $equipment = Equipment::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('delete', $equipment->toArray())
        ->assertSet('confirmingEquipmentDeletion', true)
        ->assertStatus(200);
});

it('administrator or owner can delete the specified resource in storage', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $equipment = Equipment::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->set('state.id', $equipment->id)
        ->set('password', 'password')
        ->call('destroy')
        ->assertSet('password', '')
        ->assertSet('confirmingEquipmentDeletion', false)
        ->assertEmitted('deleted-row')
        ->assertStatus(200);
});

it('administrator or owner fails on deleting the specified resource in storage with the wrong password', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $equipment = Equipment::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->set('state.id', $equipment->id)
        ->set('password', 'wrong-password')
        ->call('destroy')
        ->assertHasErrors('password')
        ->assertStatus(200);
});

it('can create dynamic input fields', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
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
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('addCustomPropertyInput')
        ->assertSet('state.custom_properties', [
            ['key' => '', 'value' => ''],
            ['key' => '', 'value' => ''],
        ])
        ->call('removeCustomPropertyInput', 0)
        ->assertEmitted('removed-custom-property')
        ->assertStatus(200);
});

it('can search data filtered - drop-down ', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->set('query', $type->name)
        ->call('search')
        ->assertSet('equipmentTypes', (new \Illuminate\Database\Eloquent\Collection))
        ->assertStatus(200);
});

it('can set the equipment type', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('selectedEquipmentType', $type)
        ->assertSet('equipmentType', $type)
        ->assertSet('state.equipment_type_id', $type->id)
        ->assertSet('query', '')
        ->assertStatus(200);
});

it('can discard the equipment type selection', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $site = Site::factory()->create();
    $type = EquipmentType::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('selectedEquipmentType', $type)
        ->assertSet('equipmentType', $type)
        ->assertSet('state.equipment_type_id', $type->id)
        ->assertSet('query', '')
        ->call('discardSelection')
        ->assertSet('equipmentType', null)
        ->assertSet('state.equipment_type_id', null)
        ->assertEmitted('discard-selected')
        ->assertStatus(200);
});

it('update equipments collection', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('updateCollection')
        ->assertDispatchedBrowserEvent('equipment-content-change')
        ->assertStatus(200);
});

it('update equipments type aggregate collection', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('updateEquipmentTypes')
        ->assertStatus(200);
});

it('can reset the component state', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $equipment = Equipment::factory()->create();
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->set('state', [
            'id' => $equipment->id,
            'name' => $equipment->name,
            'brand' => $equipment->company,
            'serial' => $equipment->serial,
            'custom_properties' => $equipment->custom_properties,
        ])
        ->call('resetState')
        ->assertSet('state', [
            'name' => '',
            'name' => '',
            'brand' => '',
            'model' => '',
            'serial' => '',
            'custom_properties' => [
                ['key' => '', 'value' => ''],
            ],
        ])
        ->assertStatus(200);
});

it('can import CSV data', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $equipment = Equipment::factory()->count(10)->create();
    $site = $equipment->first()->type->site;
    Storage::fake('s3');
    //$file = UploadedFile::fake()->create('equipment_data_example.csv');
    $file  = new UploadedFile(
        path: resource_path('examples/equipment_data_example.csv'),
        originalName: 'equipment_data_example.csv',
        mimeType: 'text/csv',
        error: null,
        test: true
    );
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->set('file', $file)
        ->call('import')
        ->assertStatus(200);
})->skip('only protected or private properties can be set as other types because JavaScript doesn\'t need to access them');

it('can export CSV data', function () {
    $user = User::factory()->create();
    $equipment = Equipment::factory()->count(10)->create();
    $site = $equipment->first()->type->site;
    $this->actingAs($user)
        ->livewire(EquipmentManagement::class, ['site' => $site])
        ->assertViewIs('livewire.equipment-management')
        ->call('export')
        ->assertStatus(200);
});
