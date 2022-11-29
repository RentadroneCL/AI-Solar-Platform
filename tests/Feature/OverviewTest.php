<?php

use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Http\Livewire\Overview;
use App\Models\{Inspection, User, Site};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains an overview livewire component', function () {
    $inspection = Inspection::factory()->create();
    $this->actingAs($user = User::factory()->withPersonalTeam()->create())
        ->livewire(Overview::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.overview')
        ->assertStatus(200);
});

it('can export CSV data', function () {
    $user = User::factory()->withPersonalTeam()->create()->assignRole('administrator');
    $inspection = Inspection::factory()->create();
    $inspection->setCustomProperty('data', [
        [
            'equipment_type_id' => 1,
            'uuid' => '3e28d494-f3ff-4d11-9dd3-392c58dd51bf',
            'filename' => 'DJI_20220106120711_0178_T',
            'latitude' => '-34.0621411944444',
            'longitude' => '-70.6488519722222',
        ],
        [
            'equipment_type_id' => 2,
            'uuid' => '3e28d494-f3ff-4d11-9dd3-392c58dd51bf',
            'filename' => 'DJI_20220106120711_0178_T',
            'latitude' => '-34.0621411944444',
            'longitude' => '-70.6488519722222',
        ],
    ]);
    $inspection->save();
    $this->actingAs($user)
        ->livewire(Overview::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.overview')
        ->call('export')
        ->assertStatus(200);
});
