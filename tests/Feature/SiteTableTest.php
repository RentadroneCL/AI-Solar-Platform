<?php

use App\Models\{User, Site};
use App\Http\Livewire\SiteTable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a users table livewire component', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(SiteTable::class, ['model' => Site::class])
        ->assertViewIs('livewire-tables::datatable')
        ->assertStatus(200);
});

it('administrator user can see all sites created', function () {
    $user = User::factory()
                ->create()
                ->assignRole('administrator');

    $this->actingAs($user)
        ->livewire(SiteTable::class, ['model' => Site::class])
        ->assertViewIs('livewire-tables::datatable')
        ->assertStatus(200);
});
