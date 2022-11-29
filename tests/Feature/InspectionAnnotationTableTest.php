<?php

use Illuminate\Support\Carbon;
use App\Models\{User, Inspection, Annotation};
use App\Http\Livewire\InspectionAnnotationTable;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Faker\faker;

uses(RefreshDatabase::class);

it('contains an inspection annotations table livewire component', function () {
    $inspection = Inspection::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(InspectionAnnotationTable::class, ['inspection' => $inspection, 'model' => Annotation::class])
        ->assertViewIs('livewire-tables::datatable')
        ->assertStatus(200);
});

it('an administrator can delete an inspection annotation', function () {
    $user = User::factory()->create()->assignRole('administrator');
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $this->actingAs($user)
        ->livewire(InspectionAnnotationTable::class, ['inspection' => $inspection, 'model' => Annotation::class])
        ->assertViewIs('livewire-tables::datatable')
        ->emit('delete-annotation', $annotation->toArray())
        ->call('delete', $annotation->toArray())
        ->assertEmitted('delete-annotation-row', $annotation->toArray())
        ->assertStatus(200);
});

it('it fails on delete if not owner or administrator', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $this->actingAs($user)
        ->livewire(InspectionAnnotationTable::class, ['inspection' => $inspection, 'model' => Annotation::class])
        ->assertViewIs('livewire-tables::datatable')
        ->emit('delete-annotation', $annotation->toArray())
        ->call('delete', $annotation->toArray())
        ->assertNotEmitted('delete-annotation-row')
        ->assertDispatchedBrowserEvent('alert')
        ->assertStatus(200);
});

it('can\'t remove the specified resource from storage if not owner or administrator', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $this->actingAs($user)
        ->livewire(InspectionAnnotationTable::class, ['inspection' => $inspection, 'model' => Annotation::class])
        ->assertViewIs('livewire-tables::datatable')
        ->call('destroySelected')
        ->assertDispatchedBrowserEvent('alert')
        ->assertStatus(200);
});
