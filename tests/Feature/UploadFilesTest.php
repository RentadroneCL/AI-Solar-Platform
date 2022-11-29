<?php

use App\Http\Livewire\UploadFiles;
use App\Models\{User, Inspection};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('upload file component', function () {
    $inspection = Inspection::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(UploadFiles::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.upload-files')
        ->assertStatus(200);
});
