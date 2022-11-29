<?php

use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Models\{Inspection, User, Site};
use App\Http\Livewire\NewInspectionModalDialog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('new inspection modal dialog', function () {
    $site = Site::factory()->create();

    $this->actingAs($user = User::factory()->withPersonalTeam()->create())
        ->livewire(NewInspectionModalDialog::class, ['site' => $site])
        ->set('state.name', faker()->name)
        ->set('state.commissioning_date', Carbon::now()->toDateTime())
        ->assertViewIs('livewire.new-inspection-modal-dialog')
        ->call('store')
        ->assertStatus(200);
});
