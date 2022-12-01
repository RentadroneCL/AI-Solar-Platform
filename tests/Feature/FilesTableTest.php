<?php

use function Pest\Faker\faker;
use App\Http\Livewire\FilesTable;
use App\Models\{User, Inspection, Media};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a files table livewire component', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(FilesTable::class, ['model' => $inspection])
        ->assertViewIs('livewire.files-table')
        ->assertStatus(200);
});

it('confirm that the user would like to delete the media resource', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $media = Media::factory()->create([
        'model_type' => Inspection::class,
        'model_id' => $inspection->id,
        'collection_name' => 'default',
        'name' => faker()->name,
        'file_name' => faker()->name,
        'mime_type' => faker()->mimeType(),
        'disk' => 's3',
        'conversions_disk' => 's3',
        'size' => faker()->randomNumber(5, false),
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
    ]);
    $this->actingAs($user)
        ->livewire(FilesTable::class, ['model' => $inspection])
        ->assertViewIs('livewire.files-table')
        ->call('confirmMediaDeletion', $media->id)
        ->assertDispatchedBrowserEvent('confirming-delete-media')
        ->assertSet('mediaId', $media->id)
        ->assertStatus(200);
})->skip('Actions - Does not support creating temporary URLs');;

it('update files collection', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(FilesTable::class, ['model' => $inspection])
        ->assertViewIs('livewire.files-table')
        ->call('updateFilesCollection')
        ->assertStatus(200);
});

it('remove the specified resource from storage', function () {
    $user = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $media = Media::factory()->create([
        'model_type' => Inspection::class,
        'model_id' => $inspection->id,
        'collection_name' => 'default',
        'name' => faker()->name,
        'file_name' => faker()->name,
        'mime_type' => faker()->mimeType(),
        'disk' => 's3',
        'conversions_disk' => 's3',
        'size' => faker()->randomNumber(5, false),
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
    ]);
    $this->actingAs($user)
        ->livewire(FilesTable::class, ['model' => $inspection])
        ->assertViewIs('livewire.files-table')
        ->call('confirmMediaDeletion', $media->id)
        ->assertDispatchedBrowserEvent('confirming-delete-media')
        ->assertSet('mediaId', $media->id)
        ->call('destroy')
        ->assertSet('confirmingMediaDeletion', false)
        ->assertEmitted('deleted-media', $media->id)
        ->assertSet('mediaId', null)
        ->assertStatus(200);
})->skip('Actions - Does not support creating temporary URLs');
