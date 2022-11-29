<?php

use App\Models\{Inspection, User};
use App\Http\Livewire\ReportViewer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a report viewer livewire component', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(ReportViewer::class, ['model' => $inspection, 'files' => $inspection->getMedia('pdf')])
        ->assertViewIs('livewire.report-viewer')
        ->assertStatus(200);
});

it('update the file collection property', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(ReportViewer::class, ['model' => $inspection, 'files' => $inspection->getMedia('pdf')])
        ->assertViewIs('livewire.report-viewer')
        ->emit('complete-files-upload')
        ->emit('deleted-media')
        ->assertStatus(200);
});
